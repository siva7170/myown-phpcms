<?php
namespace myownphpcms\core\handler;

class Switcher{
    public $router;

    public function __construct(){

    }

    public function resolve(&$router){
        $this->router=$router;
        $this->router->errorRoute=$this->router->request->errorRoute;
        $this->router->errorRouteObj=$this->getRouteObj($this->router->request->errorRoute);
        $this->findMVC();
    }

    private function getRouteObj($routeStr){
        $module="";
        $controller="";
        $action="";
        if (strpos($routeStr,"/")===false){
            // root module and index controller
            $module=$this->router->request->defaultIndexRoot;
            $controller="index";
            $action=$routeStr;
        }
        else{
              // root module
              $res=preg_match_all('/^([A-Za-z0-9]+)\/$/',$routeStr,$out);
              if($res){
                  $module=$this->router->request->defaultIndexRoot;
                  $controller=$out[1][0];
                  $action="index";
              }
              else{
                  unset($res,$out);
                  $res=preg_match_all('/^([A-Za-z0-9]+)\/([A-Za-z0-9]+)$/',$routeStr,$out);
                  if($res){
                      $module=$this->router->request->defaultIndexRoot;
                      $controller=$out[1][0];
                      $action=$out[2][0];
                  }
                  else{
                      unset($res,$out);
                      $res=preg_match_all('/^([A-Za-z0-9]+)\/([A-Za-z0-9]+)\/([A-Za-z0-9]+)$/',$routeStr,$out);
                      if($res){
                          $module=$out[1][0];
                          $controller=$out[2][0];
                          $action=$out[3][0];
                      }
                      else{
                        $module="";
                        $controller="";
                        $action="";
                      }
                  }
              }
        }
        return ["module"=>$module, "controller"=>$controller, "action"=>$action];
    }

    private function findMVC(){
        //$temp=trim($this->router->request->routePath,"/");
        $temp=$this->router->request->routePath;
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
            $res=preg_match_all('/^([A-Za-z0-9]+)\/$/',$temp,$out);
            if($res){
                $this->router->module=$this->router->request->defaultIndexRoot;
                $this->router->controller=$out[1][0];
                $this->router->action="index";
            }
            else{
                unset($res,$out);
                $res=preg_match_all('/^([A-Za-z0-9]+)\/([A-Za-z0-9]+)$/',$temp,$out);
                if($res){
                    $this->router->module=$this->router->request->defaultIndexRoot;
                    $this->router->controller=$out[1][0];
                    $this->router->action=$out[2][0];
                }
                else{
                    unset($res,$out);
                    $res=preg_match_all('/^([A-Za-z0-9]+)\/([A-Za-z0-9]+)\/([A-Za-z0-9]+)$/',$temp,$out);
                    if($res){
                        $this->router->module=$out[1][0];
                        $this->router->controller=$out[2][0];
                        $this->router->action=$out[3][0];
                    }
                    else{
                        $tempRoute=$this->getRouteObj($this->router->request->errorRoute);
                        $this->router->module=$tempRoute["module"];
                        $this->router->controller=$tempRoute["controller"];
                        $this->router->action=$tempRoute["action"];
                    }
                }
            }
        }

    }
}