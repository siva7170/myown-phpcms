<?php
namespace root\controllers;

use myownphpcms\core\mvc\Controller;
use root\models\UserLogin;

class Index extends Controller{

    public function index(){

    }

    public function login(){
        $userLogin=new UserLogin();
        $userLogin->id="";
        $userLogin->select([]);
    }
}