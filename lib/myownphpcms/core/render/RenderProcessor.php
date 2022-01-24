<?php

namespace myownphpcms\core\render;

class RenderProcessor {
    public $router;
    public $controller;
    public $controllerObj;
    public $action;
    public $module;
    public $layoutFile;

    private $modulePath;
    private $controllerFile;

    private $finalRender;

    public function __construct($router)
    {
        $this->router=$router;
        $this->findModule();
    }

    public function findModule(){
        if(file_exists($this->router->request->webFilesRoot."/".$this->router->module)){
            $this->modulePath=$this->router->request->webFilesRoot."/".$this->router->module;
            $this->findController();
            //echo "Exist";
        }
        else{
            //echo "Not Exist";
        }
    }

    public function findController(){
        $controllerFileName=ucfirst($this->router->controller).".php";
        if(file_exists($this->modulePath."/controllers/".$controllerFileName)){
            $this->controllerFile=$this->modulePath."/controllers/".$controllerFileName;
            $this->loadController();
            //echo "Exist";
        }
        else{
            //echo "Not Exist";
        }
    }

    public function loadController(){
        include $this->controllerFile;
        $controllerName=ucfirst($this->router->controller);
        $qualifiedClass='\\'.$this->router->module.'\\controllers\\'.$controllerName;
        $this->controllerObj=new $qualifiedClass;
        $this->controllerObj->setPrimaryValues($this->router->request->webFilesRoot,
            $this->router->module,$this->router->controller,$this->router->action);
        $this->controllerObj->{$this->router->action}();
        //echo '||<pre>'.print_r($this->controllerObj,true).'</pre>';
        //echo $this->controllerObj->content;
        $viewContent=$this->controllerObj->content."\n";

        $layoutFile=$this->router->request->webFilesRoot."/".$this->router->module."/layouts/".$this->controllerObj->layoutFile.".php";
        ob_start();
        include $layoutFile;
        $this->finalRender=ob_get_clean();
        $this->handOver();
    }

    public function handOver(){
        $renderFinisher=new RenderFinisher();
        $renderFinisher->setRenderOutput($this->finalRender);
        $renderFinisher->send();
    }
}