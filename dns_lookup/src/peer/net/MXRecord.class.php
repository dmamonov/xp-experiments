<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.net.Record');

  /**
   * MX
   *
   */
  class MXRecord extends peer�net�Record {
    protected $priority, $target;

    /**
     * Creates a new MX record
     *
     * @param   string name
     * @param   int priority
     * @param   string target
     */
    public function __construct($name, $priority, $target) {
      parent::__construct($name);
      $this->priority= $priority;
      $this->target= $target;
    }

    /**
     * Returns target
     *
     * @return  string
     */
    public function getTarget() {
      return $this->target;
    }

    /**
     * Returns priority
     *
     * @return  int
     */
    public function getPriority() {
      return $this->priority;
    }

    /**
     * Creates a string representation of this record
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(->'.$this->target.', pri '.$this->priority.')';
    }
  }
?>
