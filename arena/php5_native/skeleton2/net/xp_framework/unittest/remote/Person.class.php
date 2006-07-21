<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Person object
   *
   * @see      xp://net.xp_framework.unittest.remote.SerializerTest
   * @purpose  Helper class for SerializerTest
   */
  class Person extends Object {
    public
      $id     = 1549,
      $name   = 'Timm Friebe';

    /**
     * Set Id
     *
     * @access  public
     * @param   mixed id
     */
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @access  public
     * @return  mixed
     */
    public function getId() {
      return $this->id;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   mixed name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  mixed
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Checks whether a given object is equal to this person.
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool
     */
    public function equals(&$cmp) {
      return is_a($cmp, 'Person') && $cmp->name == $this->name && $cmp->id == $this->id;
    }
  }
?>
