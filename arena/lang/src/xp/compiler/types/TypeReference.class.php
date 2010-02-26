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
    protected $type= NULL;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.types.TypeName
     * @param   int kind
     */
    public function __construct(TypeName $type, $kind= parent::CLASS_KIND) {
      $this->type= $type;
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
      return $this->type->name;
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function literal() {
      return $this->type->name;
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
     * Returns whether this type is enumerable (that is: usable in foreach)
     *
     * @return  bool
     */
    public function isEnumerable() {
      return $this->type->isArray() || $this->type->isMap();
    }

    /**
     * Returns whether this class has an indexer
     *
     * @return  bool
     */
    public function hasIndexer() {
      return $this->type->isArray() || $this->type->isMap();
    }

    /**
     * Returns indexer
     *
     * @return  xp.compiler.types.Indexer
     */
    public function getIndexer() {
      if ($this->type->isArray()) {
        $i= new xp�compiler�types�Indexer();
        $i->type= $this->type->arrayComponentType();
        $i->parameters= array(new Typename('int'));
        $i->holder= $this;
        return $i;
      } else if ($this->type->isMap()) {
        $i= new xp�compiler�types�Indexer();
        $i->type= TypeName::$VAR;
        $i->parameters= array(TypeName::$VAR);
        $i->holder= $this;
        return $i;
      }
      return NULL;
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
     * Returns a property by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperty($name) {
      return FALSE;
    }
    
    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Property
     */
    public function getProperty($name) {
      return NULL;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */    
    public function toString() {
      return $this->getClassName().'@(*->'.$this->type->toString().')';
    }
    
    /**
     * Returns whether anither object is equal to this
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->type->equals($this->type);
    }
  }
?>