<?php
/* This class is part of the XP framework
 *
 * $Id: LogCategory.class.php 13756 2009-10-30 11:13:29Z kiesel $
 */

  uses('util.log.LogLevel', 'util.log.LoggingEvent');

  define('LOGGER_FLAG_INFO',    0x0001);
  define('LOGGER_FLAG_WARN',    0x0002);
  define('LOGGER_FLAG_ERROR',   0x0004);
  define('LOGGER_FLAG_DEBUG',   0x0008);
  define('LOGGER_FLAG_ALL',     LOGGER_FLAG_INFO | LOGGER_FLAG_WARN | LOGGER_FLAG_ERROR | LOGGER_FLAG_DEBUG);

  /**
   * The log category is the interface to be used. All logging information
   * is sent to a log category via one of the info, warn, error, debug 
   * methods which accept any number of arguments of any type (or 
   * their *f variants which use sprintf).
   *
   * Basic example:
   * <code>
   *   $cat= Logger::getInstance()->getCategory();
   *   $cat->addAppender(new ConsoleAppender());
   *
   *   // ...
   *   $cat->info('Starting work at', Date::now());
   *
   *   // ...
   *   $cat->debugf('Processing %d rows took %.3f seconds', $rows, $delta);
   *
   *   try {
   *     // ...
   *   } catch (SocketException $e) {
   *     $cat->warn('Caught', $e);
   *   }
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.logging.LogCategoryTest
   * @purpose  Base class
   */
  class LogCategory extends Object {
    public 
      $_appenders= array(),
      $_indicators= array(
        LogLevel::INFO        => 'info',
        LogLevel::WARN        => 'warn',
        LogLevel::ERROR       => 'error',
        LogLevel::DEBUG       => 'debug'
      );
      
    public
      $flags,
      $identifier,
      $dateformat,
      $format;

    /**
     * Constructor
     *
     * @param   string identifier
     * @param   string format 
     * @param   string dateformat
     * @param   int flags
     */
    public function __construct($identifier, $format, $dateformat, $flags= LogLevel::ALL) {
      $this->identifier= $identifier;
      $this->format= $format;
      $this->dateformat= $dateformat;
      $this->flags= $flags;
      $this->_appenders= array();
    }

    /**
     * Sets the flags (what should be logged). Note that you also
     * need to add an appender for a category you want to log.
     *
     * @param   int flags bitfield with flags (LogLevel::*)
     */
    public function setFlags($flags) {
      $this->flags= $flags;
    }
    
    /**
     * Gets flags
     *
     * @return  int flags
     */
    public function getFlags() {
      return $this->flags;
    }
    
    /**
     * Calls all appenders
     *
     */
    protected function callAppenders($event) {
      $level= $event->getLevel();
      foreach (array_keys($this->_appenders) as $appflag) {
        if (!($level & $appflag)) continue;
        foreach ($this->_appenders[$appflag] as $appender) {
          $appender->append($event);
        }
      }
    }

    /**
     * Retrieves whether this log category has appenders
     *
     * @return  bool
     */
    public function hasAppenders() {
      return !empty($this->_appenders);
    }
    
    /**
     * Finalize
     *
     */
    public function finalize() {
      foreach ($this->_appenders as $flags => $appenders) {
        foreach ($this->_appenders[$appflag] as $appender) {
          $appender->finalize();
        }
      }
    }
    
    /**
     * Adds an appender for the given log categories. Use logical OR to 
     * combine the log types or use LogLevel::ALL (default) to log all 
     * types.
     *
     * @param   util.log.LogAppender appender The appender object
     * @param   int flag default LogLevel::ALL
     * @return  util.log.LogAppender the appender added
     */
    public function addAppender($appender, $flag= LogLevel::ALL) {
      $this->_appenders[$flag][]= $appender;
      return $appender;
    }

    /**
     * Adds an appender for the given log categories and returns this
     * category - for use in a fluent interface way. Use logical OR to 
     * combine the log types or use LogLevel::ALL (default) to log all 
     * types.
     *
     * @param   util.log.LogAppender appender The appender object
     * @param   int flag default LogLevel::ALL
     * @return  util.log.LogCategory this category
     */
    public function withAppender($appender, $flag= LogLevel::ALL) {
      $this->_appenders[$flag][]= $appender;
      return $this;
    }
    
    /**
     * Remove the specified appender from the given log categories. For usage
     * of log category flags, see addAppender().
     * 
     * @param   util.log.LogAppender appender
     * @param   int flag default LogLevel::ALL
     */
    public function removeAppender($appender, $flag= LogLevel::ALL) {
      foreach ($this->_appenders as $f => $appenders) {
        if (!($f & $flag)) continue;
        
        foreach ($appenders as $idx => $apndr) {
          if ($apndr === $appender) {
            unset($this->_appenders[$f][$idx]);

            // Remove flag line, if last appender had been removed
            if (1 == sizeof($appenders)) {
              unset($this->_appenders[$f]);
            }
          }
        }
      }
    }

    /**
     * Appends a log of type info. Accepts any number of arguments of
     * any type. 
     *
     * The common rule (though up to each appender on how to realize it)
     * for serialization of an argument is:
     *
     * <ul>
     *   <li>For XP objects, the toString() method will be called
     *       to retrieve its representation</li>
     *   <li>Strings are printed directly</li>
     *   <li>Any other type is serialized using var_export()</li>
     * </ul>
     *
     * Note: This also applies to warn(), error() and debug().
     *
     * @param   mixed* args
     */
    public function info() {
      if (!($this->flags & LogLevel::INFO)) return;

      $args= func_get_args();
      $this->callAppenders(new LoggingEvent($this, time(), getmypid(), LogLevel::INFO, $args));
    }

    /**
     * Appends a log of type info in sprintf-style. The first argument
     * to this method is the format string, containing sprintf-tokens,
     * the rest of the arguments are used as argument to sprintf. 
     *
     * Note: This also applies to warnf(), errorf() and debugf().
     *
     * @see     php://sprintf
     * @param   string format 
     * @param   mixed* args
     */
    public function infof() {
      $args= func_get_args();
      $this->callAppenders(LogLevel::INFO, vsprintf($args[0], array_slice($args, 1)));
    }

    /**
     * Appends a log of type warn
     *
     * @param   mixed* args
     */
    public function warn() {
      if (!($this->flags & LogLevel::WARN)) return;

      $args= func_get_args();
      $this->callAppenders(new LoggingEvent($this, time(), getmypid(), LogLevel::WARN, $args));
    }

    /**
     * Appends a log of type info in printf-style
     *
     * @param   string format 
     * @param   mixed* args
     */
    public function warnf() {
      $args= func_get_args();
      $this->callAppenders(LogLevel::WARN, vsprintf($args[0], array_slice($args, 1)));
    }

    /**
     * Appends a log of type error
     *
     * @param   mixed* args
     */
    public function error() {
      if (!($this->flags & LogLevel::ERROR)) return;

      $args= func_get_args();
      $this->callAppenders(new LoggingEvent($this, time(), getmypid(), LogLevel::ERROR, $args));
    }

    /**
     * Appends a log of type info in printf-style
     *
     * @param   string format 
     * @param   mixed* args
     */
    public function errorf() {
      $args= func_get_args();
      $this->callAppenders(LogLevel::ERROR, vsprintf($args[0], array_slice($args, 1)));
    }

    /**
     * Appends a log of type debug
     *
     * @param   mixed* args
     */
    public function debug() {
      if (!($this->flags & LogLevel::DEBUG)) return;

      $args= func_get_args();
      $this->callAppenders(new LoggingEvent($this, time(), getmypid(), LogLevel::DEBUG, $args));
    }
 
    /**
     * Appends a log of type info in printf-style
     *
     * @param   string format format string
     * @param   mixed* args
     */
    public function debugf() {
      $args= func_get_args();
      $this->callAppenders(LogLevel::DEBUG, vsprintf($args[0], array_slice($args, 1)));
    }
   
    /**
     * Appends a separator (a "line" consisting of 72 dashes)
     *
     */
    public function mark() {
      $this->callAppenders(LogLevel::INFO, str_repeat('-', 72));
    }

    /**
     * Returns whether another object is equal to this
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self &&
        $cmp->identifier === $this->identifier
      );
    }
  }
?>