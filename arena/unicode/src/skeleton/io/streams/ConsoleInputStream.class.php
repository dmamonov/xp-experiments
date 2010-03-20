<?php
/* This class is part of the XP framework's experiments
 *
 * $Id: ConsoleInputStream.class.php 13310 2009-08-09 10:04:13Z friebe $
 */

  uses('io.streams.InputStream');

  /**
   * InputStream that reads from the console
   *
   * Usage:
   * <code>
   *   $in= new ConsoleInputStream(STDIN);
   * </code>
   *
   * @purpose  InputStream implementation
   */
  class ConsoleInputStream extends Object implements InputStream {
    protected
      $descriptor= NULL;
    
    /**
     * Constructor
     *
     * @param   resource descriptor STDIN
     */
    public function __construct($descriptor) {
      $this->descriptor= $descriptor;
    }

    /**
     * Creates a string representation of this Input stream
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->descriptor.'>';
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  lang.types.Bytes
     */
    public function read($limit= 8192) {
      if ('' === ($c= fread($this->descriptor, $limit))) return NULL;
      return new Bytes($c);
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      return feof($this->descriptor) ? 0 : 1;
    }
    
    /**
     * Close this buffer.
     *
     */
    public function close() {
      fclose($this->descriptor);
    }
  }
?>
