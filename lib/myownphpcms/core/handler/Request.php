<?php
namespace myownphpcms\core\handler;

class Request{
    // URL remote side
    public $fullURL;
    public $rootURL;
    public $pathURL;
    public $routePath;

    // Data from client
    public $reqGetData;
    public $reqPostData;

    // Server side
    public $defaultIndexRoot;
    public $webFilesRoot;
    public $appDocumentRoot;
    public $errorRoute;

    public $dbConn;

    public $urlSegments;

    public function __construct(){
        $this->routePath=$_REQUEST["url"]??"root_url";
        $this->fullURL=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"]."".$_SERVER["REQUEST_URI"];
        $this->pathURL=$_SERVER["REQUEST_URI"];
    }

    public function resolve($defaultIndexRoot,$webFilesRoot,$appInternalRoot,$appExternalRoot,$errorRoutePath, $urlSegments,$dbConn=null){
        $this->defaultIndexRoot=$defaultIndexRoot;
        $this->webFilesRoot=$webFilesRoot;
        $this->appDocumentRoot=$appInternalRoot;
        $this->rootURL=$appExternalRoot;
        $this->errorRoute=$errorRoutePath;
        $this->urlSegments=$urlSegments;
        $this->dbConn=$dbConn;
    }

    public function getRequestObj(){
        return $this;
    }
}