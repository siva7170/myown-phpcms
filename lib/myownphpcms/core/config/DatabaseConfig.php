<?php
namespace myownphpcms\core\config;

class DatabaseConfig{
    private $dbConfig;

    public function __construct($host,$user,$pass,$dbname=""){
        $this->dbConfig=[
            "host"=>$host,
            "user"=>$user,
            "pass"=>$pass,
            "dbname"=>$dbname
        ];
    }

    public function setDbConfig($host,$user,$pass,$dbname=""){
        $this->dbConfig=[
            "host"=>$host,
            "user"=>$user,
            "pass"=>$pass,
            "dbname"=>$dbname
        ];
    }

    public function getDbConfig(){
        return $this->dbConfig;
    }
}