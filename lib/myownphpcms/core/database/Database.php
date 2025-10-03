<?php
namespace myownphpcms\core\database;

use PDO;

class Database{
    private $db_conn;
    private $db_host;
    private $db_user;
    private $db_pass;
    private $db_name;
    private $sql;
    private $res;
    private $row;
    private $isConnected;
    private static $pdo;

    public function __construct($db_host='',$db_user='',$db_pass='',$db_name='')
    {
        if(strlen($db_host)>0 && strlen($db_user)>0 && strlen($db_pass)>0){
            $this->db_host=$db_host;
            $this->db_user=$db_user;
            $this->db_pass=$db_pass;
        }
        if(strlen($db_name)>0){
            $this->db_name=$db_name;
        }
        $this->db_conn=new \mysqli();
        if($this->db_conn->connect_error)
            $this->isConnected=false;
        else
            $this->isConnected=true;
    }

    public static function connectPDO($db_host='',$db_user='',$db_pass='',$db_name='') {
        if (!self::$pdo) {
            $host = $db_host;
            $dbname = $db_name;
            $user = $db_user;
            $pass = $db_pass;

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            self::$pdo = new PDO($dsn, $user, $pass);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$pdo;
    }

    public function isConnected(){
        return $this->isConnected;
    }

    public function getDbHost()
    {
        return $this->db_host;
    }

    public function getDbUser()
    {
        return $this->db_user;
    }

    public function getDbPass()
    {
        return $this->db_pass;
    }

    public function getDbName()
    {
        return $this->db_name;
    }

    public function setDbHost($db_host)
    {
        $this->db_host = $db_host;
    }

    public function setDbUser($db_user)
    {
        $this->db_user = $db_user;
    }

    public function setDbPass($db_pass)
    {
        $this->db_pass = $db_pass;
    }

    public function setDbName($db_name)
    {
        $this->db_name = $db_name;
    }

    public function connect($db_host,$db_user,$db_pass,$db_name){
        $this->db_conn = new \mysqli($db_host,$db_user,$db_pass,$db_name);
        if($this->db_conn->connect_error){
            $this->isConnected=false;
            return false;
        }
        else
        {
            $this->isConnected=true;
            return $this;
        }
    }

    public function disconnect(){
        $this->db_conn->close();
    }

    public function select_db($db_name){
        $this->db_conn->select_db($db_name);
    }

    /* Common - Start */

    public function select_query_row($sql_query){
        $this->sql = $sql_query;
        $this->res = $this->db_conn->query($this->sql);
        if(($this->res->num_rows) > 0){
            $this->row = $this->res->fetch_assoc();
            return $this->row;
        }
        else{
            return false;
        }
    }

    public function select_query_rows($sql_query){
        $this->sql = $sql_query;
        $row_data=array();
        $this->res = $this->db_conn->query($this->sql);
        if($this->res==false){
            return false;
        }
        if(($this->res->num_rows) > 0){
            while ($this->row = $this->res->fetch_assoc())
            {
                $row_data[]=$this->row;
            }
            return $row_data;
        }
        else{
            return false;
        }
    }

    public function insert_row($sql_query){
        $this->sql = $sql_query;
        if($this->db_conn->query($this->sql) == TRUE)
            return true;
        else
            return false;
    }

    public function insert_row_with_return_ins_id($sql_query){
        $this->sql = $sql_query;
        if($this->db_conn->query($this->sql) == TRUE)
            return $this->db_conn->insert_id;
        else
            return false;
    }

    public function update_row($sql_query){
        $this->sql = $sql_query;
        if($this->db_conn->query($this->sql) == TRUE)
            return true;
        else
            return false;
    }

    public function delete_row($sql_query){
        $this->sql = $sql_query;
        if($this->db_conn->query($this->sql) == TRUE)
            return true;
        else
            return false;
    }

    public function multi_query($sql,$free_result=true){
        $res=$this->db_conn->multi_query($sql);
        if($res){
            if($free_result){
                $this->free_multi_query_result();
            }
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    public function free_multi_query_result(){
        do {
            if ($result = $this->db_conn->store_result()) {
                while ($row = $result->fetch_row()) {
                }
                $result->free();
            }
            if ($this->db_conn->more_results()) {
            }
        } while ($this->db_conn->next_result());
    }

    /* Common - End */

    /* Advanced - Start */

    // for single row getting by single query
    public function select_single_query_row($sql_query){
        $this->sql = $sql_query;
        $this->res = $this->db_conn->query($this->sql);
        if(($this->res->num_rows) > 0){
            $this->row = $this->res->fetch_assoc();
            return $this->row;
        }
        else{
            return false;
        }
    }

    // for multiple row getting by single query
    public function select_single_query_rows($sql_query){
        $this->sql = $sql_query;
        $row_data=array();
        $this->res = $this->db_conn->query($this->sql);
        if($this->res==false){
            return false;
        }
        if(($this->res->num_rows) > 0){
            while ($this->row = $this->res->fetch_assoc())
            {
                $row_data[]=$this->row;
            }
            return $row_data;
        }
        else{
            return false;
        }
    }

    // for expect row(s) getting by single query
    public function select_expect_query_row($sql_query,$exp_row){
        $this->sql = $sql_query;
        $this->res = $this->db_conn->query($this->sql);
        if(($this->res->num_rows)==$exp_row){
            $this->row = $this->res->fetch_assoc();
            return $this->row;
        }
        else{
            if(!$this->db_conn->error)
                return false;
            else
                return 'ERR_2';
        }
    }

    // for result getting by single query
    public function select_single_query_result($sql_query){
        $this->sql = $sql_query;
        $this->res = $this->db_conn->query($this->sql);
        if(($this->res->num_rows) > 0){
            return true;
        }
        else{
            return false;
        }
    }

    // for result getting by single query
    public function select_expect_query_result($sql_query,$exp_row){
        $this->sql = $sql_query;
        $this->res = $this->db_conn->query($this->sql);
        if(($this->res->num_rows) ==$exp_row){
            return true;
        }
        else{
            return false;
        }
    }

    // for result getting by double query
    public function select_double_query_result($sql_query1,$sql_query2){
        $this->sql = $sql_query1;
        $this->res = $this->db_conn->query($this->sql);
        if(($this->res->num_rows) > 0){
            return 5;
        }
        else{
            $this->sql = $sql_query2;
            $this->res = $this->db_conn->query($this->sql);
            if(($this->res->num_rows) > 0){
                return 4;
            }
            else {
                return 2;
            }
        }
    }

    /* Advanced - End */

    /* Entity based - Start */

    /* Entity based - End */

    public function setReadyDb($dbArray){
        return "Okay";
    }

    public function dbInit($dbParams=null){
        return "Okay";
    }
}