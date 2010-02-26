<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents an invocation
   *
   * @see   xp://xp.compiler.ast.ChainNode
   */
  class InvocationNode extends xp�compiler�ast�Node {
    public $name= '';
    public $arguments= array();
    
    /**
     * Creates a new InvocationNode object
     *
     * @param   string name
     * @param   xp.compiler.ast.Node[] arguments
     */
    public function __construct($name, $arguments= NULL) {
      $this->name= $name;
      $this->arguments= $arguments;
    }
    
    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return $this->name.'()';
    }
  }
?>