<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.types.Types');

  /**
   * (Insert class' description here)
   *
   */
  class TypeReference extends Types {
    protected $name= '';
    
    /**
     * Constructor
     *
     * @param   string name
     * @param   int kind
     */
    public function __construct($name, $kind= parent::CLASS_KIND) {
      $this->name= $name;
      $this->kind= $kind;
    }

    /**
     * Returns parent type
     *
     * @return  xp.compiler.types.Types
     */
    public function parent() {
      return NULL;
    }
    
    /**
     * Returns name
     *
     * @return  string
     */
    public function name() {
      return $this->name;
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function literal() {
      return $this->name;
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function kind() {
      return $this->kind;
    }

    /**
     * Returns whether a constructor exists
     *
     * @return  bool
     */
    public function hasConstructor() {
      return TRUE;
    }

    /**
     * Returns the constructor
     *
     * @return  xp.compiler.types.Constructor
     */
    public function getConstructor() {
      $c= new xp�compiler�types�Constructor();
      $c->parameters= array();
      $c->holder= $this;
      return $c;
    }

    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasMethod($name) {
      return TRUE;
    }
    
    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Method
     */
    public function getMethod($name) {
      $m= new xp�compiler�types�Method();
      $m->name= $name;
      $m->returns= new TypeName('var');
      $m->parameters= array();
      $m->holder= $this;
      return $m;
    }

    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasField($name) {
      return TRUE;
    }
    
    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Field
     */
    public function getField($name) {
      $m= new xp�compiler�types�Field();
      $m->name= $name;
      $m->type= new TypeName('var');
      $m->holder= $this;
      return $m;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */    
    public function toString() {
      return $this->getClassName().'@(*->'.$this->name.')';
    }
  }
?>