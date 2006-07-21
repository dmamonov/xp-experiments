<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Transaction
   *
   * <code>
   *   uses('rdbms.DriverManager');
   *
   *   $conn= &DriverManager::getConnection('sybase://user:password@server/database');
   *   $tran= &$conn->begin(new Transaction('test'));
   *   try(); {
   *     // ... execute SQL statements
   *   } if (catch('SQLException', $e)) {
   *     $tran->rollback();
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   $tran->commit();
   * </code>
   *
   * @purpose  Wrap a transaction
   */
  class Transaction extends Object {
    public
      $name     = '',
      $db       = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    public function __construct($name) {
      $this->name= $name;
    }
    
    /**
     * Retrieve transaction state
     *
     * @access  public
     */
    public function getState() { 
      return $this->db->transtate($this->name);
    }
    
    /**
     * Rollback this transaction
     *
     * @access  public
     */
    public function rollback() { 
      return $this->db->rollback($this->name);
    }
    
    /**
     * Commit this transaction
     *
     * @access  public
     */
    public function commit() { 
      return $this->db->commit($this->name);
    }
  }
?>
