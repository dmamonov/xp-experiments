<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.ide.source.parser.Php52Parser',
    'xp.ide.source.parser.Php52Lexer',
    'xp.ide.source.Scope'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class ParserClassTest extends TestCase {

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
    public function testParseClass() {
      $tree= $this->p->parse(new xp�ide�source�parser�Php52Lexer('
        <?php
          /**
           * Test class definition
           * 
           */
          class Test extends Object {}
         ?>
       '));
       $this->assertObject($tree->getClassdef(), 'xp.ide.source.element.Classdef');
       $this->assertEquals($tree->getClassdef()->getName(), 'Test');
       $this->assertEquals($tree->getClassdef()->getParent(), 'Object');
    }

    /**
     * Test parser parses a classfile
     *
     */
    #[@test]
    public function testParseClassInterface() {
      $tree= $this->p->parse(new xp�ide�source�parser�Php52Lexer('
        <?php
          /**
           * Test class definition
           * 
           */
          class Test extends Object implements ITest, ITest2 {}
         ?>
       '));
       $this->assertEquals($tree->getClassdef()->getInterfaces(), array('ITest', 'ITest2'));
    }

    /**
     * Test parser parses a classfile
     *
     */
    #[@test]
    public function testParseMember() {
      $tree= $this->p->parse(new xp�ide�source�parser�Php52Lexer('
        <?php
          /**
           * Test class definition
           * 
           */
          class Test extends Object {
            private $member1= 1;
            public $member2= NULL;
            protected $member3= NULL, $member4;
          }
         ?>
       '));
       $this->assertEquals(array(
         new xp�ide�source�element�Classmember('member1', xp�ide�source�Scope::$PRIVATE),
         new xp�ide�source�element�Classmember('member2', xp�ide�source�Scope::$PUBLIC),
         new xp�ide�source�element�Classmember('member3', xp�ide�source�Scope::$PROTECTED),
         new xp�ide�source�element�Classmember('member4', xp�ide�source�Scope::$PROTECTED),
       ), $tree->getClassDef()->getMembers());
    }

  }
?>
