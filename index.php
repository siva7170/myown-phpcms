<?php
//require_once("lib/autoload.php");
// use myownphpcms\Application;
// $app=new Application();
// $app->init();
ini_set('display_errors',0);
register_shutdown_function('shutdown');

function shutdown()
{
  if(!is_null($e = error_get_last()))
  {
    header('content-type: text/plain');
    print "this is not html:\n\n". print_r($e,true);
  }
  else{
        header('content-type: text/plain');
        print "this is not htmlpppppp:\n\n";
  }
}

function exception_handler(Throwable $exception) {
    
    // these are our templates
    $traceline = "#%s %s(%s): %s(%s)";
    $msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

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
    $result[] = '#' . ++$keyGlobal . ' {main}';

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
o
    // log or echo as you please
    echo $msg;
}
  
  set_exception_handler('exception_handler');
  ret();
  throw new Exception('Uncaught Exception');
  echo "Not Executed\n";