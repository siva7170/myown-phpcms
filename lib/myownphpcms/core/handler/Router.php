<?php
namespace myownphpcms\core\handler;

class Router{
    public $controller;
    public $action;
    public $module;
    public $request;
    public function __construct(){

    }

    public function resolve($request){
        $this->request=$request;
    }

    public function getRouterObj(){
        return $this;
    }
}