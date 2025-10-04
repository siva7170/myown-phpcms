<?php
namespace root\controllers;

use myownphpcms\core\mvc\Controller;
use root\models\UserLogin;

class Index extends Controller{

    public function index(){
        $this->pageTitle="Set from Test Controller";
        $this->redirect("/page/viewpost/csharp/introcsharp");
        $this->layoutFile="main";
        $userLogin=UserLogin::find()->where(["user_name"=>"Siva"])->firstOrDefault();
        return $this->view("index",["t"=>$userLogin]);
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