<?php

namespace myownphpcms\core\render;

class RenderFinisher {
    private $finalOutput;

    public function send(){
        echo $this->finalOutput;
    }

    public function setRenderOutput($output){
        $this->finalOutput=$output;
    }
}