<?php
namespace myownphpcms;

use \myownphpcms\core\config\AppConfig;
use myownphpcms\core\handler\Request;
use myownphpcms\core\handler\Router;
use myownphpcms\core\handler\Switcher;

class Application{
    private $appConfig;
    private $request;
    public $router;
    private $switcher;
    public function __construct(){
        $this->request=new Request();
        $this->router=new Router();
        $this->switcher=new Switcher();
    }

    public function init($configRootPath="config"){
        // routing -> find path or show error page -> connect to controller ->if model used with DbDataModel, then init db and connect -> fetch views -> rendering view -> output the result
        // $_GET["url"]
        $appConfigObj=include $configRootPath."/appConfig.php";
        $appConfig=$appConfigObj->getAppConfig();
        $this->request->resolve($appConfig["coreConfig"]["app"]["default"]["defaultIndexRoot"],
            $appConfig["coreConfig"]["app"]["default"]["webFilesRoot"],
            $appConfig["coreConfig"]["app"]["default"]["appInternalRoot"],
            $appConfig["coreConfig"]["app"]["default"]["appExternalRoot"]);
        $this->router->resolve($this->request);
        $this->switcher->resolve($this->router);
    }
}