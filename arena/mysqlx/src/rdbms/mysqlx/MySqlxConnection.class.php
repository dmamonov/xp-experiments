<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.mysqlx.MySqlxResultSet',
    'rdbms.mysqlx.MySqlxProtocol',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.mysql.MysqlDialect'
  );

  /**
   * Connection to MySQL Databases
   *
   * @see      http://mysql.org/
   * @test     xp://net.xp_framework.unittest.rdbms.TokenizerTest
   * @test     xp://net.xp_framework.unittest.rdbms.DBTest
   * @purpose  Database connection
   */
  class MySqlxConnection extends DBConnection {

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) { 
      parent::__construct($dsn);
      $this->formatter= new StatementFormatter($this, new MysqlDialect());
      $this->handle= new MysqlxProtocol(new Socket($this->dsn->getHost(), $this->dsn->getPort(3306)));
    }

    /**
     * Connect
     *
     * @param   bool reconnect default FALSE
     * @return  bool success
     * @throws  rdbms.SQLConnectException
     */
    public function connect($reconnect= FALSE) {
      if ($this->handle->connected) return TRUE;  // Already connected
      // if (!$reconnect && (FALSE === $this->handle)) return FALSE;    // Previously failed connecting

      try {
        $this->handle->connect($this->dsn->getUser(), $this->dsn->getPassword());
        $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $reconnect));
      } catch (IOException $e) {
        $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $reconnect));
        throw new SQLConnectException($e->getMessage(), $this->dsn);
      }

      try {
        $this->handle->exec('set names LATIN1');

        // Figure out sql_mode and update formatter's escaperules accordingly
        // - See: http://bugs.mysql.com/bug.php?id=10214
        // - Possible values: http://dev.mysql.com/doc/refman/5.0/en/server-sql-mode.html
        // "modes is a list of different modes separated by comma (,) characters."
        $modes= array_flip(explode(',', current($this->handle->consume($this->handle->query(
          "show variables like 'sql_mode'"
        )))));
      } catch (IOException $e) {
        throw new SQLStatementFailedException($e->getMessage());
      }
      
      // NO_BACKSLASH_ESCAPES: Disable the use of the backslash character 
      // (\) as an escape character within strings. With this mode enabled, 
      // backslash becomes any ordinary character like any other. 
      // (Implemented in MySQL 5.0.1)
      isset($modes['NO_BACKSLASH_ESCAPES']) && $this->formatter->dialect->setEscapeRules(array(
        '"'   => '""'
      ));

      return parent::connect();
    }
    
    /**
     * Disconnect
     *
     * @return  bool success
     */
    public function close() { 
      $this->handle->close();
      return TRUE;
    }
    
    /**
     * Select database
     *
     * @param   string db name of database to select
     * @return  bool success
     * @throws  rdbms.SQLStatementFailedException
     */
    public function selectdb($db) {
      try {
        $this->handle->exec('use '.$db);
      } catch (IOException $e) {
        throw new SQLStatementFailedException($e->getMessage());
      }
    }

    /**
     * Retrieve identity
     *
     * @return  var identity value
     */
    public function identity($field= NULL) {
      $i= $this->query('select last_insert_id() as xp_id')->next('xp_id');
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $i));
      return $i;
    }

    /**
     * Retrieve number of affected rows for last query
     *
     * @return  int
     */
    protected function affectedRows() {
      return -1;    // TBI
    }    
    
    /**
     * Execute any statement
     *
     * @param   string sql
     * @param   bool buffered default TRUE
     * @return  rdbms.ResultSet or TRUE if no resultset was created
     * @throws  rdbms.SQLException
     */
    protected function query0($sql, $buffered= TRUE) {
      if (!$this->handle->connected) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect.');
      }
      
      try {
        $result= $this->handle->query($sql);
      } catch (IOException $e) {
        throw new SQLStatementFailedException($e->getMessage());
        
        // TODO: Handle other errors
      }

      if (!$buffered || $this->flags & DB_UNBUFFERED) {
        // Unbuffered
      } else {
        // TODO: Cache
      }
      
      return is_array($result) ? new MysqlxResultSet($this->handle, $result, $this->tz) : $result;
    }

    /**
     * Begin a transaction
     *
     * @param   rdbms.Transaction transaction
     * @return  rdbms.Transaction
     */
    public function begin($transaction) {
      if (!$this->query('begin')) return FALSE;
      $transaction->db= $this;
      return $transaction;
    }
    
    /**
     * Rollback a transaction
     *
     * @param   string name
     * @return  bool success
     */
    public function rollback($name) { 
      return $this->query('rollback');
    }
    
    /**
     * Commit a transaction
     *
     * @param   string name
     * @return  bool success
     */
    public function commit($name) { 
      return $this->query('commit');
    }
  }
?>