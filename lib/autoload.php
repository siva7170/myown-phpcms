<?php

ini_set('display_errors',0);
register_shutdown_function('shutdown');

function shutdown()
{
/*    if(!is_null($e = error_get_last()))
    {
        header('content-type: text/plain');
        print "this is not html:\n\n". print_r($e,true);
    }
    else{
        header('content-type: text/plain');
        print "this is not htmlpppppp:\n\n";
    }*/
}

function exception_handler(Throwable $exception) {
    header('content-type: text/html');


    $traceline = "<li>#%s %s(%s): %s(%s)</li>";
    $msg = "Exception:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

    // alter your trace as you please, here
    $trace = $exception->getTrace();
    foreach ($trace as $key => $stackPoint) {
        // I'm converting arguments to their type
        // (prevents passwords from ever getting logged as anything other than 'string')
        $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
    }

    $keyGlobal=0;

    // build your tracelines
    $result = array();
    foreach ($trace as $key => $stackPoint) {
        $result[] = sprintf(
            $traceline,
            $key,
            $stackPoint['file'],
            $stackPoint['line'],
            $stackPoint['function'],
            implode(', ', $stackPoint['args'])
        );
        $keyGlobal=$key;
    }
    // trace always ends with {main}
    $result[] = '<li>#' . ++$keyGlobal . ' {main}</li>';

    // write tracelines into main template
    $msg = sprintf(
        $msg,
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        implode("\n", $result),
        $exception->getFile(),
        $exception->getLine()
    );

    $resultStr=implode("\n", $result);
    $className=get_class($exception);

    // these are our templates
    $template=<<<EXCEPPAGE
<!DOCTYPE html>
<html>
<head>
<title>Exception Occurred</title>
</head>
<body>
    <h3>{$exception->getMessage()}</h3>
    <hr/>
    <br/>
    <p>Exception: <b>{$className}</b></p>
    <p>Message: <b>{$exception->getMessage()}</b></p>
    <p>File: <b>{$exception->getFile()}</b></p>
    <p>Line No.: <b>{$exception->getLine()}</b></p>
    <p>Stack Trace:<br/>
        <ul>
        {$resultStr}
        </ul>
    </p>
</body>
</html>
EXCEPPAGE;

    // log or echo as you please
    echo $template;
}

set_exception_handler('exception_handler');

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


