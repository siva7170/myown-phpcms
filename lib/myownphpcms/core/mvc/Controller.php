<?php

namespace myownphpcms\core\mvc;

class Controller extends View {
    public $dbConn;
    public $urlSegmentsParams=[];
    public function setPrimaryValues($webFilesRoot,$module,$controller,$action,$dbConn=[]){
        $this->webFilesRoot=$webFilesRoot;
        $this->module=$module;
        $this->controller=$controller;
        $this->action=$action;
        $this->dbConn=$dbConn;
    }

    protected function beforeProceed(){

    }

    protected function afterProceed(){

    }
}
