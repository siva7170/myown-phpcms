<?php

namespace myownphpcms\core\mvc;

class Controller extends View {

    public function setPrimaryValues($webFilesRoot,$module,$controller,$action){
        $this->webFilesRoot=$webFilesRoot;
        $this->module=$module;
        $this->controller=$controller;
        $this->action=$action;
    }

    protected function beforeProceed(){

    }

    protected function afterProceed(){

    }
}
