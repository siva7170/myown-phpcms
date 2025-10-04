<?php
namespace myownphpcms\core\mvc;

use myownphpcms\core\exception\MOException;

class View {
   public $content;
   public $viewFile;
   public $layoutFile;
   public $viewFile_full;

   public $pageTitle;

   public $webFilesRoot;
    public $controller;
    public $action;
    public $module;

   public function __construct()
   {

   }

   public function redirect($url, $statusCode = 302) {
        header("Location: " . $url, true, $statusCode);
        exit;
   }

   public function view($viewFile, $valArg=[]){
       $viewSlashes=substr_count($viewFile,"/");
       $this->viewFile=$viewFile;
       if($viewSlashes==0){
           $this->viewFile_full=$this->webFilesRoot."/".$this->module."/views/".$this->controller."/".$viewFile.".php";
       }
       else if($viewSlashes==1){
           $vRoutes=explode("/",$viewFile);
           $this->viewFile_full=$this->webFilesRoot."/".$this->module."/views/".$vRoutes[0]."/".$vRoutes[1].".php";
       }
       else if($viewSlashes==2){
           $vRoutes=explode("/",$viewFile);
           $this->viewFile_full=$this->webFilesRoot."/".$this->module."/".$vRoutes[0]."/".$vRoutes[1]."/".$vRoutes[2].".php";
       }
       if(file_exists($this->viewFile_full)){
           if (is_array($valArg)) {
               extract($valArg);
           }
        ob_start();
        include $this->viewFile_full;
        $this->content=ob_get_clean();
        return $this;
       }
       else{
           throw new MOException("\"$viewFile\" view is not found in $this->module\\$this->controller");
       }
      
   }

   public function viewPartial($viewFile, $valArg=[]){
       $viewSlashes=substr_count($viewFile,"/");
       $this->viewFile=$viewFile;
       if($viewSlashes==0){
           $this->viewFile_full=$this->webFilesRoot."/".$this->module."/views/".$this->controller."/".$viewFile.".php";
       }
       else if($viewSlashes==1){
           $vRoutes=explode("/",$viewFile);
           $this->viewFile_full=$this->webFilesRoot."/".$this->module."/views/".$vRoutes[0]."/".$vRoutes[1].".php";
       }
       else if($viewSlashes==2){
           $vRoutes=explode("/",$viewFile);
           $this->viewFile_full=$this->webFilesRoot."/".$this->module."/".$vRoutes[0]."/".$vRoutes[1]."/".$vRoutes[2].".php";
       }
        if(file_exists($this->viewFile_full)){
            if (is_array($valArg)) {
                extract($valArg);
            }
            ob_start();
            include $this->viewFile_full;
            $this->content=ob_get_clean();
            return $this->content;
        }
        else{
            throw new MOException("\"$viewFile\" view is not found in $this->module\\$this->controller");
        }

    }
}