<?php
namespace root\controllers;

use myownphpcms\core\mvc\Controller;

class Error extends Controller{

    public function index(){
        $this->layoutFile="error";
        return $this->view("index");
    }

    public function notfound(){
        $this->layoutFile="error";
        return $this->view("notfound");
    }
}