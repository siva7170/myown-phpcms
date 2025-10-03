<?php
namespace myownphpcms\core\mvc;
use myownphpcms\core\exception\MOException;

class View {
   public $content;
   public $viewFile;
   public $layoutFile;
   public $viewFile_full;

   public $webFilesRoot;
    public $controller;
    public $action;
    public $module;

   public function __construct()
   {

   }

   public function view($viewFile, $valArg=[]){
       $this->viewFile=$viewFile;
       $this->viewFile_full=$this->webFilesRoot."/".$this->module."/views/".$this->controller."/".$viewFile.".php";
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
}