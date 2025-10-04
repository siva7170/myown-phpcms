<?php

namespace myownphpcms\core\render;

use myownphpcms\core\database\Database;
use myownphpcms\core\database\DbDataModel;
use myownphpcms\core\database\DbOperations;

class RenderProcessor {
    public $router;
    public $controller;
    public $controllerObj;
    public $action;
    public $module;
    public $layoutFile;

    public $dbConn;
    public $dbConfig;

    private $modulePath;
    private $controllerFile;

    private $finalRender;

    public function __construct($router)
    {
        $this->router=$router;
        $this->dbConfig=$router->request->dbConn->getDbConfig();
        //$this->dbConn=new Database();
        //$this->dbConn->connect($this->dbConfig["host"],$this->dbConfig["user"],$this->dbConfig["pass"],$this->dbConfig["dbname"]);
        $this->findModule();
    }

    public function loadAllModels($baseDir) {
        // Scan all subfolders in base directory
        $subFolders = array_filter(glob($baseDir . '/*'), 'is_dir');

        foreach ($subFolders as $folder) {
            $modelsFolder = $folder . '/models';
            if (is_dir($modelsFolder)) {
                // Include all PHP files in models folder
                foreach (glob($modelsFolder . '/*.php') as $file) {
                    include_once $file;
                }
            }
        }
    }


    public function findModule($errorCallback=false){
        if(file_exists($this->router->request->webFilesRoot."/".$this->router->module)){
            $this->modulePath=$this->router->request->webFilesRoot."/".$this->router->module;
            $this->findController($errorCallback);
            //echo "Exist";
        }
        else{
            //echo "Not Exist";
            if($errorCallback){
                $this->createSelfErrorPage();
            }
            else{
                $this->modulePath=$this->router->request->webFilesRoot."/".$this->router->errorRouteObj["module"];
                $this->router->module=$this->router->errorRouteObj["module"];
                $this->router->controller=$this->router->errorRouteObj["controller"];
                $this->router->action=$this->router->errorRouteObj["action"];
                $this->findController($errorCallback);
            }
        }
    }

    public function findController($errorCallback=false){
        $controllerFileName=ucfirst($this->router->controller).".php";
        if(file_exists($this->modulePath."/controllers/".$controllerFileName)){
            $this->controllerFile=$this->modulePath."/controllers/".$controllerFileName;
            $this->loadController($errorCallback);
            //echo "Exist";
        }
        else{
            //echo "Not Exist";
            if($errorCallback){
                $this->createSelfErrorPage();
            }
            else{
                $this->modulePath=$this->router->request->webFilesRoot."/".$this->router->errorRouteObj["module"];
                $this->router->module=$this->router->errorRouteObj["module"];
                $this->router->controller=$this->router->errorRouteObj["controller"];
                $this->router->action=$this->router->errorRouteObj["action"];
                $this->loadController($errorCallback);
            }
        }
    }

    public function loadController($errorCallback=false){
        include $this->controllerFile;
        $this->loadAllModels($this->router->request->webFilesRoot);
        DbDataModel::setDb(Database::connectPDO($this->dbConfig["host"],$this->dbConfig["user"],$this->dbConfig["pass"],$this->dbConfig["dbname"]));
        $controllerName=ucfirst($this->router->controller);
        $qualifiedClass='\\'.$this->router->module.'\\controllers\\'.$controllerName;
        if(class_exists($qualifiedClass)){
            $this->controllerObj=new $qualifiedClass;
            if(method_exists($this->controllerObj,$this->router->action)){
                $this->controllerObj->setPrimaryValues($this->router->request->webFilesRoot,
                $this->router->module,$this->router->controller,$this->router->action);
                $this->controllerObj->urlSegmentsParams=$this->router->urlSegmentsObj;

                $this->controllerObj->{$this->router->action}();
                //echo '||<pre>'.print_r($this->controllerObj,true).'</pre>';
                //echo $this->controllerObj->content;
                $viewContent=$this->controllerObj->content."\n";
                $layoutFile=$this->router->request->webFilesRoot."/".$this->router->module."/layouts/".$this->controllerObj->layoutFile.".php";
                $pageTitle=$this->controllerObj->pageTitle;
                ob_start();
                include $layoutFile;
                $this->finalRender=ob_get_clean(); 
                $this->handOver();
            }
            else{
                if($errorCallback){
                    $this->createSelfErrorPage();
                }
                else{
                    $this->modulePath=$this->router->request->webFilesRoot."/".$this->router->errorRouteObj["module"];
                    $this->router->module=$this->router->errorRouteObj["module"];
                    $this->router->controller=$this->router->errorRouteObj["controller"];
                    $this->router->action=$this->router->errorRouteObj["action"];
                    $this->findModule(true);
                }
            }
        }
        else{
            if($errorCallback){
                $this->createSelfErrorPage();
            }
            else{
                $this->modulePath=$this->router->request->webFilesRoot."/".$this->router->errorRouteObj["module"];
                $this->router->module=$this->router->errorRouteObj["module"];
                $this->router->controller=$this->router->errorRouteObj["controller"];
                $this->router->action=$this->router->errorRouteObj["action"];
                $this->findModule(true);
            }
        }
    }

    public function createSelfErrorPage(){
        ob_start();
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Error - 404 Page Not Found</title>
        </head>
        <body>
            The requested page is not found on this site. Please try again later or go to home.
        </body>
        </html>';
        $this->finalRender=ob_get_clean();
        $this->handOver();
    }

    public function handOver(){
        //$this->dbConn->disconnect();
        $renderFinisher=new RenderFinisher();
        $renderFinisher->setRenderOutput($this->finalRender);
        $renderFinisher->send();
    }
}