<?php
namespace myownphpcms\core\handler;

class Router{
    public $controller;
    public $action;
    public $module;
    public $urlSegmentsObj=[];
    public $request;
    public $errorRoute;
    public $errorRouteObj;
    public function __construct(){

    }

    public function resolve($request){
        $this->request=$request;
    }

    public function getRouterObj(){
        return $this;
    }
}