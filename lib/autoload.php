<?php
class autoload{
    static public function loader($className){
        $filename="lib/".str_replace("\\","/",$className).".php";
        if(file_exists($filename)){
            include($filename);
            if(class_exists($className)){
                return true;
            }
        }
        return false;
    }
}
spl_autoload_register('autoload::loader');