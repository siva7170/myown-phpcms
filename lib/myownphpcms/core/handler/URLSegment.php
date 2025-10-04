<?php
namespace myownphpcms\core\handler;

class URLSegment{
    public $controller;
    public $action;
    public $module;
    public $urlSegmentResolver;

    public function __construct($module,$controller,$action,$urlSegmentResolver){
        $this->module=$module;
        $this->controller=$controller;
        $this->action=$action;
        $this->urlSegmentResolver=$urlSegmentResolver;
    }

    public function getURLSegmentResolver(){
        return $this->urlSegmentResolver;
    }
}