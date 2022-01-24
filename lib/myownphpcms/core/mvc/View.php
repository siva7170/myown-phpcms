<?php
namespace myownphpcms\core\mvc;

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

   public function view($viewFile){
       $this->viewFile=$viewFile;
       $this->viewFile_full=$this->webFilesRoot."/".$this->module."/views/".$this->controller."/".$viewFile.".php";
       ob_start();
       include $this->viewFile_full;
       $this->content=ob_get_clean();
       return $this;
   }
}