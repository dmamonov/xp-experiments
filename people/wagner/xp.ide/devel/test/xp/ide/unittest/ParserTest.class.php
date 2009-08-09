<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.ide.source.parser.Php52Parser',
    'xp.ide.source.parser.Php52Lexer'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class ParserTest extends TestCase {

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->p= new xp�ide�source�parser�Php52Parser();
    }

    /**
     * Test parser parses a classfile
     *
     */
    #[@test]
    public function testParseClassFile() {
      $this->assertObject($this->p->parse(new xp�ide�source�parser�Php52Lexer('
        <?php ?>
      ')), 'xp.ide.source.element.ClassFile');
    }
  }
?>
