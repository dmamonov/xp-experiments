<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Abstract base class for appenders
   *
   * @see      xp://util.log.LogCategory#addAppender
   * @purpose  Base class
   */
  abstract class LogAppender extends Object {

    /**
     * Append data
     *
     * @model   abstract
     * @access  public
     * @param   mixed* args
     */ 
    public abstract function append();
 
    /**
     * Finalize this appender. This method is called at shutdown
     *
     * @access  public
     */   
    public function finalize() { }
    
    /**
     * Retrieve a readable representation of a variable
     *
     * @access  protected
     * @param   mixed var
     * @return  string
     */
    protected function varSource($var) {
      if ($var instanceof Generic) {
        return $var->toString();
      }
      return is_string($var) ? $var : var_export($var, 1);
    }
  }
?>
