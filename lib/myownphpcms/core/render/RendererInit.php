<?php

namespace myownphpcms\core\render;

use myownphpcms\core\render\RenderProcessor;

class RendererInit extends RenderProcessor {
    private $switcher;
    private $renderCollector;
    public function __construct()
    {

    }

    public function resolve($switcher){
        $this->switcher=$switcher;
        $this->forwardToCollector();
    }

    public function forwardToCollector(){
        $this->renderCollector=new RenderProcessor($this->switcher->router);
    }
}