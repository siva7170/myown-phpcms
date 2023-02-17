<?php
namespace root\controllers;

use myownphpcms\core\mvc\Controller;
use root\models\UserLogin;

class Page extends Controller{

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
        $this->layoutFile="inner";
        return $this->view("show");
    }

    public function show(){
        $this->layoutFile="inner";
        return $this->view("show");
    }
}