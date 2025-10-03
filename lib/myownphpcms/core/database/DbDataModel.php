<?php
namespace myownphpcms\core\database;

use Exception;
use myownphpcms\core\mvc\Model;

abstract class DbDataModel {
    protected $attributes = [];
    protected $fields = [];
    protected $table;
    protected $pk = "id";
    protected static $db;

    public static function setDb($db) {
        self::$db = $db;
    }

    public static function db() {
        return self::$db;
    }

    public function __construct($data = []) {
        $this->table = $this->setTableName();
        $this->fields = $this->useFields();

        // initialize attributes array with null
        foreach ($this->fields as $field) {
            $this->attributes[$field] = null;
        }

        // preload data if given
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fields)) {
                $this->attributes[$key] = $value;
            }
        }
    }

    // child classes must implement these
    abstract protected function setTableName();
    abstract protected function useFields();

    public static function tableName() {
        $instance = new static();
        return $instance->setTableName();
    }


    // magic getter
    public function __get($name) {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        throw new Exception("Undefined property: $name");
    }

    // magic setter
    public function __set($name, $value) {
        if (in_array($name, $this->fields)) {
            $this->attributes[$name] = $value;
            return;
        }
        throw new Exception("Undefined property: $name");
    }

    // optional: check if field is set
    public function __isset($name) {
        return isset($this->attributes[$name]);
    }

    // --- Query Builder entry point ---
    public static function find() {
        return new DbOperations(static::class);
    }

    // --- CRUD: Save (Insert/Update) ---
    public function save() {
        $pdo = self::db();

        if ($this->attributes[$this->pk]) {
            // update
            $fields = array_filter($this->fields, fn($f) => $f !== $this->pk);
            $set = implode(", ", array_map(fn($f) => "$f = :$f", $fields));
            $sql = "UPDATE {$this->table} SET $set WHERE {$this->pk} = :{$this->pk}";
        } else {
            // insert
            $fields = array_filter($this->fields, fn($f) => $f !== $this->pk);
            $columns = implode(",", $fields);
            $params = ":" . implode(",:", $fields);
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
        }

        $stmt = $pdo->prepare($sql);

        // bind data
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $this->attributes[$field];
        }
        if ($this->attributes[$this->pk]) {
            $data[$this->pk] = $this->attributes[$this->pk];
        }

        $stmt->execute($data);

        // set ID after insert
        if (!$this->attributes[$this->pk]) {
            $this->attributes[$this->pk] = $pdo->lastInsertId();
        }
    }

    // --- CRUD: Delete ---
    public function delete() {
        if (!$this->attributes[$this->pk]) {
            throw new Exception("Cannot delete unsaved record");
        }
        $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = :id";
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['id' => $this->attributes[$this->pk]]);
    }

//    // --- CRUD Methods ---
//
//    // find by primary key
//    public static function find($id) {
//        $instance = new static(); // late static binding
//        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->pk} = :id LIMIT 1";
//        $stmt = self::db()->prepare($sql);
//        $stmt->execute(['id' => $id]);
//        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
//
//        return $row ? new static($row) : null;
//    }
//
//    // get all records
//    public static function all() {
//        $instance = new static();
//        $sql = "SELECT * FROM {$instance->table}";
//        $stmt = self::db()->query($sql);
//        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//
//        $models = [];
//        foreach ($rows as $row) {
//            $models[] = new static($row);
//        }
//        return $models;
//    }
//
//    // insert/update
//    public function save() {
//        $pdo = self::db();
//
//        if ($this->attributes[$this->pk]) {
//            // update
//            $fields = array_filter($this->fields, fn($f) => $f !== $this->pk);
//            $set = implode(", ", array_map(fn($f) => "$f = :$f", $fields));
//            $sql = "UPDATE {$this->table} SET $set WHERE {$this->pk} = :{$this->pk}";
//        } else {
//            // insert
//            $fields = array_filter($this->fields, fn($f) => $f !== $this->pk);
//            $columns = implode(",", $fields);
//            $params = ":" . implode(",:", $fields);
//            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($params)";
//        }
//
//        $stmt = $pdo->prepare($sql);
//
//        // bind data
//        $data = [];
//        foreach ($fields as $field) {
//            $data[$field] = $this->attributes[$field];
//        }
//        if ($this->attributes[$this->pk]) {
//            $data[$this->pk] = $this->attributes[$this->pk];
//        }
//
//        $stmt->execute($data);
//
//        // set ID after insert
//        if (!$this->attributes[$this->pk]) {
//            $this->attributes[$this->pk] = $pdo->lastInsertId();
//        }
//    }
//
//    // delete record
//    public function delete() {
//        if (!$this->attributes[$this->pk]) {
//            throw new Exception("Cannot delete unsaved record");
//        }
//        $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = :id";
//        $stmt = self::db()->prepare($sql);
//        $stmt->execute(['id' => $this->attributes[$this->pk]]);
//    }
}

//
//class DbDataModel extends Model{
//    protected $tableName="";
//
//    public function __construct()
//    {
//        $this->prepareTblCol($this->useFields());
//    }
//
//    protected function prepareTblCol($colArr){
//        foreach ($colArr as $k=>$v){
//            $this->{$v}="";
//        }
//    }
//
//    protected function setTableName(){
//
//    }
//
//    protected function setFieldName()
//    {
//
//    }
//
//    protected function useFields(){
//
//    }
//
//    public function select($tblColumns){
//        $this->db_cols=$tblColumns;
//        return $this;
//    }
//
//    public function where($tblCondition){
//        $this->db_condition=$tblCondition;
//        return $this;
//    }
//
//    public function order($tblorder){
//        $this->db_order=$tblorder;
//        return $this;
//    }
//
//    public function limit($tbllimit){
//        $this->db_limit=$tbllimit;
//        return $this;
//    }
//
//    protected function buildQuery(){
//        switch ($this->tbl_operation_name){
//            case 'CREATE':
//                $temp_name=array();
//                $temp_val=array();
//                $this->db_query_str='INSERT INTO `'.$this->db_tbl.'` ';
//                $class_vars = get_object_vars($this->vFields);
//
//                foreach ($class_vars as $name => $value) {
//                    if($value!=''){
//                        $temp_name[]=$name;
//                        $temp_val[]=$value;
//                    }
//                }
//
//                foreach ($temp_name as $k=>$v){
//                    $temp_name[$k]='`'.$v.'`';
//                }
//                $this->db_query_str.='('.join(', ',$temp_name).') VALUES ';
//
//                foreach ($temp_val as $k=>$v){
//                    $temp_val[$k]='\''.$v.'\'';
//                }
//                $this->db_query_str.='('.join(', ',$temp_val).')';
//                break;
//            case 'READ':
//                // SELECT
//                $this->db_query_str='SELECT ';
//                if($this->db_cols!=null){
//                    foreach ($this->db_cols as $k=>$v){
//                        $this->db_cols[$k]=($v=='*')?'*':'`'.$v.'`';
//                    }
//                    $this->db_query_str.=join(', ',$this->db_cols).' ';
//                }
//                else{
//                    $this->db_query_str.='* ';
//                }
//                // FROM
//                $this->db_query_str.='FROM `'.$this->db_tbl.'` ';
//                // WHERE
//                if($this->db_condition!=null){
//                    $this->db_query_str.='WHERE '.$this->recursiveWhere($this->db_condition).' ';
//                }
//                // ORDER BY
//                if($this->db_order!=null){
//                    foreach ($this->db_order as $k=>$v){
//                        $order_by=($v=='asc')?('ASC'):('DESC');
//                        $this->db_order[$k]='`'.$k.'` '.$order_by;
//                    }
//                    $this->db_query_str.='ORDER BY '.join(', ',$this->db_order).' ';
//                }
//                // LIMIT
//                if($this->db_limit!=null){
//                    $this->db_query_str.='LIMIT ';
//                    if(count($this->db_limit)==2){
//                        $this->db_query_str.=$this->db_limit[0].','.$this->db_limit[1];
//                    }
//                    else{
//                        $this->db_query_str.=$this->db_limit[0];
//                    }
//                }
//                break;
//            case 'UPDATE':
//                $temp_nv=array();
//                $this->db_query_str='UPDATE `'.$this->db_tbl.'` SET ';
//                $this->updateRow(); // Make nly needed cols
//                $class_vars = get_object_vars($this->vFields);
//                foreach ($class_vars as $name => $value) {
//                    if($value!='' && $this->tbl_pri_field!=$name){
//                        $temp_nv[]='`'.$name.'`=\''.$value.'\'';
//                    }
//                }
//                // WHERE
//                if($this->db_condition!=null){
//                    $this->db_query_str.=join(', ',$temp_nv).' WHERE '.$this->recursiveWhere($this->db_condition).' ';
//                }
//                else{
//                    $this->db_query_str.=join(', ',$temp_nv).' WHERE `'.$this->tbl_pri_field.'`=\''.$this->vFields->{$this->tbl_pri_field}.'\'';
//                }
//
//                break;
//            case 'DELETE':
//                $this->db_query_str='DELETE FROM `'.$this->db_tbl.'` ';
//                if($this->db_condition!=null){
//                    $this->db_query_str.='WHERE '.$this->recursiveWhere($this->db_condition).' ';
//                }
//                else{
//                    $this->db_query_str.='WHERE `'.$this->tbl_pri_field.'`=\''.$this->vFields->{$this->tbl_pri_field}.'\'';
//                }
//                break;
//            default:
//                $this->db_query_str='';
//        }
//
//    }
//
//    public function getQuery($q_type=''){
//        if($q_type=='READ' || $q_type=='DELETE'){
//            $this->tbl_operation_name=$q_type;
//        }
//        $this->buildQuery();
//        return $this->db_query_str;
//    }
//
//    protected function reset(){
//        $this->db_query_str='';
//        $this->db_cols=null;
//        $this->db_condition=null;
//        $this->db_order=null;
//        $this->db_limit=null;
//    }
//
//    protected function recursiveWhere($whereArray){
//        $temp='';
//        if(count($whereArray)==3){
//            $arr_cond=($whereArray[0]=='and')?('AND'):(($whereArray[0]=='or')?('OR'):($whereArray[0]));
//            $temp='('.$this->recursiveWhere($whereArray[1]).' '.$arr_cond.' '.$this->recursiveWhere($whereArray[2]).')';
//        }
//        elseif(count($whereArray)==1){
//            $temp=$this->formWhereConditionBuild($whereArray);
//        }
//        return $temp;
//    }
//
//    protected function formWhereConditionBuild($whereArray){
//        $temp_key=array_keys($whereArray);
//        $temp_value=array_values($whereArray);
//        return '`'.$temp_key[0].'`=\''.str_replace("'","'/",$temp_value[0]).'\'';
//    }
//
//    protected function loadTblInit(){
//        $sql="SHOW COLUMNS FROM `".$this->db_tbl."`";
//        $res=$this->db_conn->select_query_rows($sql);
//        foreach($res as $r){
//            if($r["Key"]=="PRI"){
//                $this->tbl_pri_field=$r["Field"];
//            }
//            $this->tbl_operation_fields[]=$r["Field"];
//        }
//        if($this->tbl_pri_field==''){
//            $this->tbl_pri_field=$this->tbl_operation_fields[0];
//        }
//    }
//
//    public function getRow(){
//        $this->tbl_operation_name='READ';
//        $sql=$this->getQuery();
//        $res=$this->db_conn->select_query_row($sql);
//        if($res!=false){
//            $this->vFields=new VirtualFields();
//            foreach($res as $k=>$v){
//                $this->vFields->addValues($k,$v);
//            }
//            $this->reset();
//            return $this->vFields;
//        }
//        else{
//            return null;
//        }
//    }
//
//    public function getRows(){
//        $this->tbl_operation_name='READ';
//        $sql=$this->getQuery();
//        $res=$this->db_conn->select_query_rows($sql);
//        $this->vFields=array();
//        foreach($res as $k=>$v){
//            $this->vFields[$k]=new VirtualFields();
//            foreach($v as $k1=>$v1){
//                $this->vFields[$k]->addValues($k1,$v1);
//            }
//        }
//        $this->reset();
//        return $this->vFields;
//    }
//
//    public function getRowsAsArray(){
//        $this->tbl_operation_name='READ';
//        $sql=$this->getQuery();
//        $res=$this->db_conn->select_query_rows($sql);
//        return $res;
//    }
//
//    public function prepareAdd(){
//        $this->reset();
//        $this->tbl_operation_name='CREATE';
//        $sql="SHOW COLUMNS FROM `".$this->db_tbl."`";
//        $res=$this->db_conn->select_query_rows($sql);
//        $this->vFields=new VirtualFields();
//        foreach($res as $r){
//            $this->vFields->addValues($r["Field"],"");
//        }
//        return $this->vFields;
//    }
//
//    public function prepareUpdate(){
//        $this->tbl_operation_name='UPDATE';
//        /* $sql="SHOW COLUMNS FROM `".$this->db_tbl."`";
//         $res=$this->db_conn->select_query_rows($sql);
//         $this->vFields=new VirtualFields();
//         foreach($res as $r){
//             $this->vFields->addValues($r["Field"],"");
//         }
//         return $this->vFields;*/
//        $sql="SELECT * FROM `".$this->db_tbl."` WHERE ".$this->recursiveWhere($this->db_condition);
//        $res=$this->db_conn->select_expect_query_row($sql,1);
//        $this->vFields=new VirtualFields();
//        if($res!=false){
//            foreach($res as $k=>$v){
//                $this->vFields->addValues($k,$v);
//            }
//            return $this->vFields;
//        }
//        else{
//            return false;
//        }
//    }
//
//    public function updateRow(){
//        $sql="SELECT * FROM `".$this->db_tbl."` WHERE ".$this->recursiveWhere($this->db_condition);
//        $res=$this->db_conn->select_expect_query_row($sql,1);
//        if($res!=false){
//            foreach($res as $k=>$v){
//                if($this->vFields->{$k}==$v){
//                    unset($this->vFields->{$k});
//                }
//            }
//            return true;
//        }
//        else{
//            return false;
//        }
//    }
//
//    public function save(){
//        $this->tbl_operation_name=($this->tbl_operation_name=='INIT')?'CREATE':'UPDATE';
//        $sql=$this->getQuery();
//        if($this->tbl_operation_name=='CREATE'){
//            $new_id=$this->db_conn->insert_row_with_return_ins_id($sql);
//            $this->vFields->{$this->tbl_pri_field}=$new_id;
//        }
//        else{
//            $this->db_conn->update_row($sql);
//        }
//        return $this;
//    }
//
//    public function delete(){
//        $this->tbl_operation_name='DELETE';
//        $sql=$this->getQuery();
//        $this->db_conn->delete_row($sql);
//        $this->reset();
//        $this->vFields=new VirtualFields();
//        return $this;
//    }
//}