<?php
namespace myownphpcms\core\handler;

class Switcher{
    public $router;

    public function __construct(){

    }

    public function resolve(&$router){
        $this->router=$router;
        $this->findMVC();
    }

    private function findMVC(){
        $temp=trim($this->router->request->routePath,"/");
        if($temp=="root_url"){
            // default one
            $this->router->module=$this->router->request->defaultIndexRoot;
            $this->router->controller="index";
            $this->router->action="index";
        }
        elseif (strpos($temp,"/")===false){
            // root module and index controller
            $this->router->module=$this->router->request->defaultIndexRoot;
            $this->router->controller="index";
            $this->router->action=$temp;
        }
        else{
            // root module
            $res=preg_match_all('/^(.*?)\/(.*?)$/',$temp,$out);
            if($res){
                $this->router->module=$this->router->request->defaultIndexRoot;
                $this->router->controller=$out[1][0];
                $this->router->action=$out[2][0];
            }
            else{
                unset($res,$out);
                $res=preg_match_all('/^(.*?)\/(.*?)\/(.*?)$/',$temp,$out);
                if($res){
                    $this->router->module=$out[1][0];
                    $this->router->controller=$out[2][0];
                    $this->router->action=$out[3][0];
                }
                else{
                    // show error page
                }
            }
        }

    }
}