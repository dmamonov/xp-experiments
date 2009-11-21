<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Enum');

  /**
   * (Insert class' description here)
   *
   * @see      xp://xml.streams.XmlStreamReader
   */
  class XmlEvent extends Enum {
    public static 
      $START_ELEMENT,
      $END_ELEMENT,
      $PROCESSING_INSTRUCTION,
      $CHARACTERS,
      $COMMENT,
      $END_DOCUMENT;
    
    static function __static() {
      self::$START_ELEMENT= new self(1, 'START_ELEMENT');
      self::$END_ELEMENT= new self(2, 'END_ELEMENT');
      self::$PROCESSING_INSTRUCTION= new self(3, 'PROCESSING_INSTRUCTION');
      self::$CHARACTERS= new self(4, 'CHARACTERS');
      self::$COMMENT= new self(5, 'COMMENT');
      self::$END_DOCUMENT= new self(6, 'END_DOCUMENT');
    }
    
    /**
     * Returns all enum members
     *
     * @return  lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
    }
  }
?>
