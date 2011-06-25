<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'peer.net';

  /**
   * Represents a DNS message
   *
   */
  class peer�net�Message extends Object {
    protected $id= 0;
    protected $flags= 0;
    protected $opcode= 0;
    protected $type= 0;
    protected $records= array();

    /**
     * Creates an ID for this message
     *
     * @return  int
     */
    protected static function id() {
      static $id= 1;

      if (++$id > 65535) $id= 1;
      return $id;
    }
  
    /**
     * Creates a new message
     *
     * @param   int id default NULL if omitted, an ID is generated
     */
    public function __construct($id= NULL) {
      $this->id= (NULL === $id) ? self::id() : $id;
    }

    /**
     * Gets id
     *
     * @return  int
     */
    public function getId() {
      return $this->id;
    }

    /**
     * Sets id
     *
     * @param   int id
     */
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Gets flags
     *
     * @return  int
     */
    public function getFlags() {
      return $this->flags;
    }

    /**
     * Sets flags
     *
     * @param   int flags
     */
    public function setFlags($flags) {
      $this->flags= $flags;
    }
    
    /**
     * Gets opcode
     *
     * @return  int
     */
    public function getOpcode() {
      return $this->opcode;
    }

    /**
     * Sets opcode
     *
     * @param   int opcode
     */
    public function setOpcode($opcode) {
      $this->opcode= $opcode;
    }

    /**
     * Adds a record
     *
     * @param   peer.net.Record record
     */
    public function addRecord($record) {
      $this->records[]= $record;
    }

    /**
     * Gets all records
     *
     * @return  peer.net.Record[]
     */
    public function getRecords() {
      return $this->records;
    }
  }
?>
