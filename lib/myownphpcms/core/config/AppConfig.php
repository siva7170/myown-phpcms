<?php
namespace myownphpcms\core\config;

class AppConfig{
    private $appConfig;

    public function __construct($appConfig){
        $this->appConfig=$appConfig;
    }

    public function getAppConfig(){
        return $this->appConfig;
    }
}