<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.OutputStream');

  /**
   * Writes XML in a streaming fashion
   *
   * @ext      iconv
   * @test     xp://test.XmlStreamWriterTest
   */
  class XmlStreamWriter extends Object {
    protected $stream       = NULL;
    protected $opened       = NULL;
    protected $encoding     = '';
    
    /**
     * Creates a new XML stream writer
     *
     * @param   io.streams.OutputStream stream
     */
    public function __construct(OutputStream $stream) {
      $this->stream= $stream;
    }
 
    /**
     * Starts document
     *
     * @param   string encoding default "iso-8859-1"
     * @throws  lang.IllegalStateException in case document has already been started
     */
    public function startDocument($encoding= 'iso-8859-1') {
      if (NULL !== $this->opened) {
        throw new IllegalStateException('Document already started');
      }
      $this->stream->write('<?xml version="1.0" encoding="'.$encoding.'"?>');
      $this->encoding= $encoding;
      $this->opened= array();
    }

    /**
     * Closes a node
     *
     */
    protected function writeEnd($name) {
      $this->stream->write('</'.$name.'>');
    }

    /**
     * Ends document. Closes any open tags
     *
     * @throws  lang.IllegalStateException in case document has not yet been started
     */
    public function endDocument() {
      if (NULL === $this->opened) {
        throw new IllegalStateException('Document not yet started');
      }
      while ($name= array_pop($this->opened)) {
        $this->writeEnd($name);
      }
    }

    /**
     * Starts a node
     *
     * @param   string name
     * @param   array<string, string> attributes
     */
    public function startNode($name, array $attributes= array()) {
      $this->stream->write('<'.$name);
      foreach ($attributes as $attribute => $value)  {
        $this->stream->write(' '.$attribute.'="'.htmlspecialchars($value, ENT_QUOTES).'"');
      }
      $this->stream->write('>');
      $this->opened[]= $name;
    }
    
    /**
     * Closes a node
     *
     */
    public function endNode() {
      $this->writeEnd(array_pop($this->opened));
    }

    /**
     * Writes an empty node
     *
     * @param   string name
     */
    public function writeEmptyNode($name) {
      $this->stream->write('<'.$name.'/>');
    }

    /**
     * Write character data
     *
     * @param   string text
     */
    public function writeCharacters($text) {
      $this->stream->write(htmlspecialchars(iconv('iso-8859-1', $this->encoding, $text), ENT_NOQUOTES));
    }

    /**
     * Write CDATA section
     *
     * @param   string text
     */
    public function writeCData($text) {
      $this->stream->write('<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $text).']]>');
    }

    /**
     * Write comment
     *
     * @param   string text
     */
    public function writeComment($text) {
      $this->stream->write('<!-- '.$text.' -->');
    }

    /**
     * Write processing instruction
     *
     * @param   string target
     * @param   string text
     */
    public function writeProcessingInstruction($target, $text) {
      $this->stream->write('<?'.$target.' '.$text.' ?>');
    }

    /**
     * Write an entity reference
     *
     * @param   string name
     */
    public function writeEntityRef($name) {
      $this->stream->write('&'.$name.';');
    }
  }
?>
