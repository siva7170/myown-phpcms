<?php
namespace root\controllers;

use myownphpcms\core\mvc\Controller;
use root\models\UserLogin;

class Index extends Controller{

    public function index(){
        $this->layoutFile="main";
        return $this->view("index");
    }

    public function login(){
        $userLogin=new UserLogin();
        $userLogin->id="";
        $userLogin->select([]);
    }

    public function showMe(){
        $this->layoutFile="main";
        return $this->view("index");
    }
}