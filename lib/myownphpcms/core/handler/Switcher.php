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

        // [e.g.: https://localhost]
        if (strpos($routeStr,"/")===false){
            // root module and index controller
            $module=$this->router->request->defaultIndexRoot;
            $controller="index";
            $action=$routeStr;
        }
        else{
              // root module [e.g.: https://localhost/post/]
              $res=preg_match_all('/^([A-Za-z0-9]+)\/$/',$routeStr,$out);
              if($res){
                  $module=$this->router->request->defaultIndexRoot;
                  $controller=$out[1][0];
                  $action="index";
              }
              else{
                  unset($res,$out);
                  // [e.g.: https://localhost/post/view]
                  $res=preg_match_all('/^([A-Za-z0-9]+)\/([A-Za-z0-9]+)$/',$routeStr,$out);
                  if($res){
                      $module=$this->router->request->defaultIndexRoot;
                      $controller=$out[1][0];
                      $action=$out[2][0];
                  }
                  else{
                      unset($res,$out);
                      // [e.g.: https://localhost/user/post/view]
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
        $isURLSegmentFound=false;
        $temp=$this->router->request->routePath;
        // URL Segment

        $tempURLSegs=$this->router->request->urlSegments??null;
        if($tempURLSegs!=null){
            foreach ($tempURLSegs as $pattern => $segment) {
                if (preg_match("~$pattern~", $temp, $matches)) {
                    // remove full match
                    array_shift($matches);

                    $params = [];
                    foreach ($segment->getURLSegmentResolver() as $key => $name) {
                        $index = (int) trim($key, "[]");
                        if (isset($matches[$index - 1])) {
                            $params[$name] = $matches[$index - 1];
                        }
                    }
                    $this->router->module=$segment->module;
                    $this->router->controller=$segment->controller;
                    $this->router->action=$segment->action;
                    $this->router->urlSegmentsObj=$params;
                    $isURLSegmentFound=true;
                    break;
                }
            }
        }

        if(!$isURLSegmentFound){
            //$temp=trim($this->router->request->routePath,"/");

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
}