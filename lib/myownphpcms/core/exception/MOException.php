<?php
namespace myownphpcms\core\exception;

class MOException extends \Exception{
    /*protected $message = 'Unknown exception';     // Exception message
    private   $string;                            // Unknown
    protected $code    = 0;                       // User-defined exception code
    protected $file;                              // Source filename of exception
    protected $line;                              // Source line of exception
    private   $trace;                             // Unknown

    protected $details;*/

 /* public function __construct($details) {
      //$this->details = $details;
      parent::__construct();
  }

  public function __toString() {
    return 'I am an exception. Here are the deets: ' . $this->details;
  } */

     public function __construct($message = null, $code = 0)
     {
         if (!$message) {
             throw new $this('Unknown '. get_class($this));
         }
         parent::__construct($message, $code);
     }

     public function __toString()
     {
         return " '{$this->message}' in {$this->file}({$this->line})\n"
                                 . "{$this->getTraceAsString()}";
     }
}