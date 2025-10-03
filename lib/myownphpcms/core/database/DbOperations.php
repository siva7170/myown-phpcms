<?php
namespace myownphpcms\core\database;

use Exception;
use PDO;

class DbOperations{
    protected $modelClass;
    protected $table;
    protected $fields = "*";
    protected $where = [];
    protected $params = [];
    protected $limit = null;
    protected $offset = null;
    protected $order = "";
    protected $group = "";

    public function __construct($modelClass) {
        $this->modelClass = $modelClass;
        $obj = new $modelClass();
        $this->table = $obj->tableName();
    }

    // --- Select ---
    public function select($fields) {
        $this->fields = is_array($fields) ? implode(",", $fields) : $fields;
        return $this;
    }

    // --- Where conditions ---
    public function where($condition) {
        $this->where[] = $this->buildCondition($condition);
        return $this;
    }

    public function andWhere($condition) {
        $this->where[] = ['AND', $this->buildCondition($condition)];
        return $this;
    }

    public function orWhere($condition) {
        $this->where[] = ['OR', $this->buildCondition($condition)];
        return $this;
    }

    // --- Group & Order ---
    public function groupBy($fields) {
        $this->group = is_array($fields) ? implode(",", $fields) : $fields;
        return $this;
    }

    public function orderBy($order) {
        $this->order = $order;
        return $this;
    }

    // --- Limit & Offset ---
    public function limit($n) {
        $this->limit = intval($n);
        return $this;
    }

    public function offset($n) {
        $this->offset = intval($n);
        return $this;
    }

    // --- Build condition (AND/OR, IN, = ) ---
    protected function buildCondition($condition) {
        if (!is_array($condition)) {
            throw new Exception("Condition must be array");
        }

        $op = strtolower($condition[0] ?? '');
        if ($op === 'and' || $op === 'or') {
            $parts = [];
            for ($i=1; $i<count($condition); $i++) {
                $parts[] = $this->buildCondition($condition[$i]);
            }
            return '(' . implode(" " . strtoupper($op) . " ", $parts) . ')';
        }

        // simple key=>value
        $clauses = [];
        foreach ($condition as $col => $val) {
            if (is_array($val)) {
                // IN clause
                $placeholders = [];
                foreach ($val as $v) {
                    $param = "p" . count($this->params);
                    $placeholders[] = ":$param";
                    $this->params[$param] = $v;
                }
                $clauses[] = "$col IN (" . implode(",", $placeholders) . ")";
            } else {
                $param = "p" . count($this->params);
                $clauses[] = "$col = :$param";
                $this->params[$param] = $val;
            }
        }
        return implode(" AND ", $clauses);
    }

    // --- SQL builder ---
    protected function buildSql() {
        $sql = "SELECT {$this->fields} FROM {$this->table}";
        if ($this->where) {
            $sql .= " WHERE " . $this->normalizeWhere();
        }
        if ($this->group) {
            $sql .= " GROUP BY {$this->group}";
        }
        if ($this->order) {
            $sql .= " ORDER BY {$this->order}";
        }
        if ($this->limit !== null) {
            $sql .= " LIMIT " . $this->limit;
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET " . $this->offset;
        }
        return $sql;
    }

    protected function normalizeWhere() {
        $sql = "";
        foreach ($this->where as $idx => $w) {
            if (is_array($w)) {
                [$op, $expr] = $w;
                $sql .= ($idx == 0) ? $expr : " $op $expr";
            } else {
                $sql .= ($idx == 0) ? $w : " AND $w";
            }
        }
        return $sql;
    }

    // --- Execution ---
    public function all() {
        $sql = $this->buildSql();
        $stmt = DbDataModel::db()->prepare($sql);
        $stmt->execute($this->params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];
        foreach ($rows as $row) {
            $models[] = new $this->modelClass($row);
        }
        return $models;
    }

    public function allArray() {
        $sql = $this->buildSql();
        $stmt = DbDataModel::db()->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first() {
        $this->limit = 1;
        $sql = $this->buildSql();
        $stmt = DbDataModel::db()->prepare($sql);
        $stmt->execute($this->params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new $this->modelClass($row) : null;
    }

    public function firstOrDefault($default = null) {
        $res = $this->first();
        return $res ?? $default;
    }

    public function count() {
        $sql = "SELECT COUNT(*) as cnt FROM {$this->table}";
        if ($this->where) {
            $sql .= " WHERE " . $this->normalizeWhere();
        }
        $stmt = DbDataModel::db()->prepare($sql);
        $stmt->execute($this->params);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    }

    public function list($keyField, $valueField) {
        $sql = $this->buildSql();
        $stmt = DbDataModel::db()->prepare($sql);
        $stmt->execute($this->params);
        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[$row[$keyField]] = $row[$valueField];
        }
        return $result;
    }
}