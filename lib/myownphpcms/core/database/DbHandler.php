<?php

use cls\Config\DbConn;
use cls\Database\Database;
use cls\Database\Table;

class DbHandler
{
    private $dbConnections;
    private $dbSetArray;

    public function __construct()
    {
        $this->dbConnections=DbConn::getDbConnDetails();
    }

    public function setReadyDb($dbArray){
        $this->dbSetArray=$dbArray;
    }

    public function init(){
        foreach($this->dbSetArray as $dbSet){
            $dbConn=$this->search_by_value($this->dbConnections,$dbSet[0]);
            $db_conn=$this->dbConnections[$dbConn];
            if($dbConn>-1){
                $dbInit=new Database();
                $dbTbl=new Table($dbInit->connect($db_conn[0],
                    $db_conn[1],
                    $db_conn[2],
                    $db_conn[3]),$dbSet[1]);
                $dbSet[2]=$dbTbl->init();
            }
            else{
                $dbSet[2]=null;
            }
        }
    }

    private function search_by_value($arr,$val){
        foreach ($arr as $k=>$v){
            if($v[3]==$val){
                return $k;
            }
        }
        return -1;
    }

}