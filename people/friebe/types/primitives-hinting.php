<?php
  function __error($code, $message, $file, $line, $scope) {
    static $primitives= array(    // Mapping notation in source -> string produced by gettype() 
      'string' => 'string',
      'int'    => 'integer',
      'bool'   => 'boolean',
      'float'  => 'float'
    );
    
    if (E_RECOVERABLE_ERROR === $code) {
      sscanf($message, 'Argument %d passed to %s must be an instance of %[^,], %s given', 
        $offset,
        $callable,
        $restriction,
        $type
      );
      
      if (isset($primitives[$restriction]) && $primitives[$restriction] == $type) return TRUE;
      throw new InvalidArgumentException(
        $callable.': Argument '.$offset.' must be of type '.$restriction.', '.$type.' given'
      );
    }
  }
  set_error_handler('__error');
  
  class Printer {
    public function println(string $s) {
      echo $s, "\n";
    }

    public function dump(array $a, int $indent) {
      $prefix= str_repeat(' ', $indent);
      echo 'array [', "\n";
      foreach ($a as $k => $v) {
        echo $prefix, $k, ' => ', $v, "\n";
      }
      echo ']', "\n";
    }
  }
  
  // {{{ main
  $p= new Printer();
  $p->println('Hello');
  $p->dump(array(1, 2, 3), 2);
  
  try {
    $p->println(array());
  } catch (InvalidArgumentException $expected) {
    echo 'Caught expected ', $expected->getMessage(), "\n";
  }

  try {
    $p->dump(array(1, 2, 3), '2');
  } catch (InvalidArgumentException $expected) {
    echo 'Caught expected ', $expected->getMessage(), "\n";
  }
  // }}}
?>
