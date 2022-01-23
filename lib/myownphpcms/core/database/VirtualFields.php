<?php
namespace myownphpcms\core\database;

class VirtualFields{
    public function addValues($key,$value){
        $this->{$key}=$value;
    }

    public function getAll(){
        return $this;
    }
}