<?php
/* This file is part of the XP framework's experiments
 *
 * $Id: FileInputStream.class.php 14236 2010-02-16 10:13:59Z friebe $
 */

  uses('io.streams.InputStream', 'io.streams.Seekable', 'io.File');

  /**
   * InputStream that reads from a file
   *
   * @test     xp://net.xp_framework.unittest.io.streams.FileInputStreamTest
   * @purpose  InputStream implementation
   */
  class FileInputStream extends Object implements InputStream, Seekable {
    protected
      $file= NULL;
    
    /**
     * Constructor
     *
     * @param   var file either an io.File object or a string
     */
    public function __construct($file) {
      $this->file= $file instanceof File ? $file : new File($file);
      $this->file->open(FILE_MODE_READ);
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  lang.types.Bytes
     */
    public function read($limit= 8192) {
      if ('' === ($chunk= $this->file->read($limit))) return NULL;
      return new Bytes($chunk);
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     * @return  int
     */
    public function available() {
      return $this->file->size() - $this->file->tell();
    }

    /**
     * Close this buffer
     *
     */
    public function close() {
      $this->file->close();
    }

    /**
     * Creates a string representation of this file
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->file->toString().'>';
    }

    /**
     * Seek to a given offset
     *
     * @param   int offset
     * @param   int whence default SEEK_SET (one of SEEK_[SET|CUR|END])
     * @throws  io.IOException in case of error
     */
    public function seek($offset, $whence= SEEK_SET) {
      $this->file->seek($offset, $whence);
    }

    /**
     * Return current offset
     *
     * @return  int offset
     */
    public function tell() {
      return $this->file->tell();
    }
  }
?>
