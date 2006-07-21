<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ClassNotFoundException');

  /**
   * Represents a collection of objects of the same class (or subclass)
   *
   * <code>
   *   $coll= &Collection::forClass('rdbms.DBConnection');
   *
   *   $coll->add(new SybaseConnection(...));       // Works
   *   $coll->add(new MySQLConnection(...));        // Works, too
   *   $coll->add(Date::now());                     // Fails
   *   $coll->add(1);                               // Fails
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.core.CollectionTest
   * @purpose  "Type-safe" array
   */
  class Collection extends Object {
    public
      $class   = '',
      $list    = array();
      
    public
      $_name   = '';
    
    /**
     * Constructor
     *
     * @access  protected
     * @param   string class
     */
    public function __construct($class) {
      $this->class= $class;
      $this->_name= xp::reflect($class);
    }
    
    /**
     * Returns a new Collection object for a specified class
     *
     * @access  public
     * @param   string class the fully qualified class name
     * @return  &lang.Collection
     * @throws  lang.ClassNotFoundException
     */
    public function &forClass($class) {
      if (!class_exists(xp::reflect($class))) {
        throw(new ClassNotFoundException('Class "'.$class.'" does not exist'));
      }
      $c= &new Collection($class);
      return $c;
    }
    
    /**
     * Returns the number of elements in this list.
     *
     * @access  public
     * @return  int
     */
    public function size() {
      return sizeof($this->list);
    }
    
    /**
     * Returns the element's class name
     *
     * @access  public
     * @return  string
     */
    public function getElementClassName() {
      return $this->class;
    }

    /**
     * Returns the element's class name
     *
     * @access  public
     * @return  &lang.XPClass
     */
    public function &getElementClass() {
      return XPClass::forName($this->class);
    }
    
    /**
     * Tests if this list has no elements.
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty() {
      return empty($this->list);
    }
    
    /**
     * Adds an element to this list
     *
     * @access  public
     * @param   &lang.Object element
     * @return  &lang.Object the added element
     * @throws  lang.IllegalArgumentException
     */
    public function &add(&$element) {
      if (!is($this->_name, $element)) {
        throw(new IllegalArgumentException(sprintf(
          'Element is not a %s (but %s)',
          $this->class,
          xp::typeOf($element)
        )));
      }
      $this->list[]= &$element;
      return $element;
    }

    /**
     * Adds an element to the beginning of this list
     *
     * @access  public
     * @param   &lang.Object element
     * @return  &lang.Object the prepended element
     * @throws  lang.IllegalArgumentException
     */
    public function &prepend(&$element) {
      if (!is($this->_name, $element)) {
        throw(new IllegalArgumentException(sprintf(
          'Element is not a %s (but %s)',
          $this->class,
          xp::typeOf($element)
        )));
      }
      array_unshift($this->list, $element);
      return $element;
    }

    /**
     * Adds an array of elements to this list
     *
     * @access  public
     * @param   lang.Object[] array
     * @throws  lang.IllegalArgumentException
     */
    public function addAll($array) {
      $original= $this->list;
      for ($i= 0, $s= sizeof($array); $i < $s; $i++) {
        if (!is($this->_name, $array[$i])) {
          $this->list= $original;   // Rollback
          throw(new IllegalArgumentException(sprintf(
            'Element %d is not a %s (but %s)',
            $i,
            $this->class,
            xp::typeOf($array[$i])
          )));
        }
        $this->list[]= &$array[$i];
      }
    }

    /**
     * Prepend an array of elements to this list
     *
     * @access  public
     * @param   lang.Object[] array
     * @throws  lang.IllegalArgumentException
     */
    public function prependAll($array) {
      $original= $this->list;
      for ($i= 0, $s= sizeof($array); $i < $s; $i++) {
        if (!is($this->_name, $array[$i])) {
          $this->list= $original;   // Rollback
          throw(new IllegalArgumentException(sprintf(
            'Element %d is not a %s (but %s)',
            $i,
            $this->class,
            xp::typeOf($array[$i])
          )));
        }
        array_unshift($this->list, $array[$i]);
      }
    }

    /**
     * Replaces the element at the specified position in this list with 
     * the specified element.
     *
     * @access  public
     * @param   int index
     * @param   &lang.Object element
     * @return  &lang.Object the element previously at the specified position.
     */
    public function &set($index, &$element) {
      $orig= &$this->list[$index];
      $this->list[$index]= &$element;
      return $orig;
    }
        
    /**
     * Returns the element at the specified position in this list.
     *
     * @access  public
     * @param   int index
     * @return  &lang.Object
     */
    public function &get($index) {
      return $this->list[$index];
    }
    
    /**
     * Removes the element at the specified position in this list.
     * Shifts any subsequent elements to the left (subtracts one 
     * from their indices).
     *
     * @access  public
     * @param   int index
     * @return  &lang.Object the element that was removed from the list
     */
    public function &remove($index) {
      $element= &$this->list[$index];
      unset($this->list[$index]);
      $this->list= array_values($this->list);
      return $element;
    }
    
    /**
     * Removes all of the elements from this list. The list will be empty 
     * after this call returns.
     *
     * @access  public
     */
    public function clear() {
      $this->list= array();
    }
    
    /**
     * Returns an array of this list's elements
     *
     * @access  public
     * @return  lang.Object[]
     */
    public function values() {
      return array_values($this->list);
    }
    
    /**
     * Checks if a value exists in this array
     *
     * @access  public
     * @param   &lang.Object element
     * @return  bool
     */
    public function contains(&$element) {
      for ($i= 0, $s= sizeof($this->list); $i < $s; $i++) {
        if ($this->list[$i]->equals($element)) return TRUE;
      }
      return FALSE;
    }
    
    /**
     * Searches for the first occurence of the given argument
     *
     * @access  public
     * @param   &lang.Object element
     * @return  int offset where the element was found or FALSE
     */
    public function indexOf(&$element) {
    
      // Note: array_search() does NOT work for objects:
      //
      // <snip from="ext/standard/array.c">
      // if (Z_TYPE_PP(value) == IS_OBJECT) {
      //     php_error_docref(NULL TSRMLS_CC, E_WARNING, "Wrong datatype for first argument");
      //     RETURN_FALSE;
      // }
      // </snip>
      for ($i= 0, $s= sizeof($this->list); $i < $s; $i++) {
        if ($this->list[$i]->equals($element)) return $i;
      }
      return FALSE;
    }

    /**
     * Searches for the last occurence of the given argument
     *
     * @access  public
     * @param   &lang.Object element
     * @return  int offset where the element was found or FALSE
     */
    public function lastIndexOf(&$element) {
      for ($i= sizeof($this->list)- 1; $i > -1; $i--) {
        if ($this->list[$i]->equals($element)) return $i;
      }
      return FALSE;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $r= $this->getClassName().'<'.$this->class.">@{\n";
      for ($i= 0, $s= sizeof($this->list); $i < $s; $i++) {
        $r.= '  '.$i.': '.str_replace("\n", "\n  ", xp::stringOf($this->list[$i]))."\n";
      } 
      return $r.'}';
    }
    
    /**
     * Checks if a specified object is equal to this object.
     *
     * @access  public
     * @param   &lang.Object collection
     * @return  bool
     */
    public function equals(&$collection) {
      if (
        !is('Collection', $collection) || 
        $this->size != $collection->size
      ) return FALSE;
      
      // Compare element by element
      for ($i= 0, $s= sizeof($this->list); $i < $s; $i++) {
        if ($this->list[$i]->equals($collection->list[$i])) continue;
        return FALSE;
      }
      return TRUE;
    }
  }
?>
