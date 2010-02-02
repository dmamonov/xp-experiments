<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.types.TaskScope',
    'xp.compiler.diagnostic.NullDiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.task.CompilationTask'
  );

  /**
   * TestCase
   *
   */
  abstract class ExecutionTest extends TestCase {
    protected static $syntax;
    
    protected $scope;
    protected $emitter;
    protected $counter= 0;
  
    /**
     * Sets up compiler API
     *
     */
    #[@beforeClass]
    public static function setupCompilerApi() {
      self::$syntax= Syntax::forName('xp');
    }
    
    /**
     * Adds a check
     *
     * @param   xp.compiler.checks.Checks c
     * @param   bool error
     */
    protected function check(Check $c, $error= FALSE) {
      $this->emitter->addCheck($c, $error);
    }
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->emitter= new xp�compiler�emit�source�Emitter();
      $this->scope= new TaskScope(new CompilationTask(
        new FileSource(new File(__FILE__), self::$syntax),
        new NullDiagnosticListener(),
        new FileManager(),
        $this->emitter
      ));
      $this->counter= 0;
    }
    
    /**
     * Run statements and return result
     *
     * @param   string src
     * @param   string[] imports
     * @return  var
     */
    protected function run($src, array $imports= array()) {
      return $this->define(
        'class', 
        ucfirst($this->name).'�'.($this->counter++), 
        NULL,
        '{ public void run() { '.$src.' }}',
        $imports
      )->newInstance()->run();
    }

    /**
     * Compile statements and return type
     *
     * @param   string src
     * @param   string[] imports
     * @return  lang.XPClass
     */
    protected function compile($src, array $imports= array()) {
      return $this->define(
        'class', 
        ucfirst($this->name).'�'.($this->counter++), 
        NULL,
        '{ public void run() { '.$src.' }}',
        $imports
      );
    }
    
    /**
     * Define class from a given name and source
     *
     * @param   string type
     * @param   string class
     * @param   string parent
     * @param   string src
     * @param   string[] imports
     * @return  lang.XPClass
     */
    protected function define($type, $class, $parent, $src, array $imports= array()) {
      $class= 'Source'.$class;
      $r= $this->emitter->emit(
        self::$syntax->parse(new MemoryInputStream(
          implode("\n", $imports).
          ' public '.$type.' '.$class.' '.($parent ? ' extends '.$parent : '').$src
        ), $this->name), 
        $this->scope
      );
      xp::gc();

      $r->executeWith(array());
      return new XPClass($class);
    }
  }
?>
