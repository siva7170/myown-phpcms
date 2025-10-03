<?php
namespace myownphpcms;

use cls\Database\DbHandler;
use myownphpcms\core\config\AppConfig;
use myownphpcms\core\handler\Request;
use myownphpcms\core\handler\Router;
use myownphpcms\core\handler\Switcher;
use myownphpcms\core\render\RendererInit;

class Application{
    private $appConfig;
    public $db;
    private $request;
    public $router;
    private $switcher;
    private $render;
    public function __construct(){
        $this->request=new Request();
        $this->router=new Router();
        $this->switcher=new Switcher();
        $this->render=new RendererInit();
        //$this->db=new DbHandler();
    }

    public function init($configRootPath="config"){
        // routing -> find path or show error page -> connect to controller ->if model used with DbDataModel, then init db and connect -> fetch views -> rendering view -> output the result
        // $_GET["url"]
        $appConfigObj=include $configRootPath."/appConfig.php";
        $appConfig=$appConfigObj->getAppConfig();
        $this->request->resolve($appConfig["coreConfig"]["app"]["default"]["defaultIndexRoot"],
            $appConfig["coreConfig"]["app"]["default"]["webFilesRoot"],
            $appConfig["coreConfig"]["app"]["default"]["appInternalRoot"],
            $appConfig["coreConfig"]["app"]["default"]["appExternalRoot"],
            $appConfig["coreConfig"]["app"]["default"]["errorRoute"],
            $appConfig["coreConfig"]["app"]["default"]["dbConfig"][0]
        );
        $this->router->resolve($this->request);
        $this->switcher->resolve($this->router);
        $this->render->resolve($this->switcher);
        //echo '<pre>'.print_r($this->switcher->router,true).'</pre>';
    }
}