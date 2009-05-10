<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class AssignmentTest extends ParserTestCase {
  
    /**
     * Test assigning to a variable
     *
     */
    #[@test]
    public function toVariable() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('i'),
        'expression'    => new IntegerNode(array('value' => '0')),
        'op'            => '='
      ))), $this->parse('$i= 0;'));
    }

    /**
     * Test assigning to a variable via "+="
     *
     */
    #[@test]
    public function addAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('i'),
        'expression'    => new IntegerNode(array('value' => '1')),
        'op'            => '+='
      ))), $this->parse('$i += 1;'));
    }

    /**
     * Test assigning to a variable via "-="
     *
     */
    #[@test]
    public function subAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('i'),
        'expression'    => new IntegerNode(array('value' => '1')),
        'op'            => '-='
      ))), $this->parse('$i -= 1;'));
    }

    /**
     * Test assigning to a variable via "-="
     *
     */
    #[@test]
    public function mulAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('i'),
        'expression'    => new IntegerNode(array('value' => '2')),
        'op'            => '*='
      ))), $this->parse('$i *= 2;'));
    }

    /**
     * Test assigning to a variable via "/="
     *
     */
    #[@test]
    public function divAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('i'),
        'expression'    => new IntegerNode(array('value' => '2')),
        'op'            => '/='
      ))), $this->parse('$i /= 2;'));
    }

    /**
     * Test assigning to a variable via "%="
     *
     */
    #[@test]
    public function modAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('i'),
        'expression'    => new IntegerNode(array('value' => '2')),
        'op'            => '%='
      ))), $this->parse('$i %= 2;'));
    }

    /**
     * Test assigning to a variable via "~="
     *
     */
    #[@test]
    public function concatAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('s'),
        'expression'    => new StringNode(array('value' => '.')),
        'op'            => '~='
      ))), $this->parse('$s ~= ".";'));
    }

    /**
     * Test assigning to a variable via ">>="
     *
     */
    #[@test]
    public function shiftRightAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('s'),
        'expression'    => new IntegerNode(array('value' => '2')),
        'op'            => '>>='
      ))), $this->parse('$s >>= 2;'));
    }

    /**
     * Test assigning to a variable via "<<="
     *
     */
    #[@test]
    public function shiftLeftAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('s'),
        'expression'    => new IntegerNode(array('value' => '2')),
        'op'            => '<<='
      ))), $this->parse('$s <<= 2;'));
    }

    /**
     * Test assigning to a variable via "|="
     *
     */
    #[@test]
    public function orAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('s'),
        'expression'    => new IntegerNode(array('value' => '2')),
        'op'            => '|='
      ))), $this->parse('$s |= 2;'));
    }

    /**
     * Test assigning to a variable via "&="
     *
     */
    #[@test]
    public function andAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('s'),
        'expression'    => new IntegerNode(array('value' => '2')),
        'op'            => '&='
      ))), $this->parse('$s &= 2;'));
    }

    /**
     * Test assigning to a variable via "|="
     *
     */
    #[@test]
    public function xorAssign() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('s'),
        'expression'    => new IntegerNode(array('value' => '2')),
        'op'            => '^='
      ))), $this->parse('$s ^= 2;'));
    }

    /**
     * Test assigning to a variable with array offset
     *
     */
    #[@test]
    public function toArrayOffset() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new ChainNode(array(
          0 => new VariableNode('i'),
          1 => new ArrayAccessNode(array(
            'offset' => new IntegerNode(array('value' => '0'))
          )),
        )),
        'expression'    => new IntegerNode(array('value' => '0')),
        'op'            => '='
      ))), $this->parse('$i[0]= 0;'));
    }

    /**
     * Test assigning to a variable with array offset
     *
     */
    #[@test]
    public function appendToArray() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new ChainNode(array(
          0 => new VariableNode('i'),
          1 => new ArrayAccessNode(array('offset' => NULL)),
        )),
        'expression'    => new IntegerNode(array('value' => '0')),
        'op'            => '='
      ))), $this->parse('$i[]= 0;'));
    }

    /**
     * Test assigning to an instance member
     *
     */
    #[@test]
    public function toInstanceMember() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new ChainNode(array(
          0 => new VariableNode('class'), 
          1 => new VariableNode('member'),
        )),
        'expression'    => new IntegerNode(array('value' => '0')),
        'op'            => '='
      ))), $this->parse('$class.member= 0;'));
    }

    /**
     * Test assigning to a class member
     *
     */
    #[@test]
    public function toClassMember() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new ClassMemberNode(array(
          'class'         => new TypeName('self'),
          'member'        => new VariableNode('instance')
        )),
        'expression'    => new NullNode(),
        'op'            => '='
      ))), $this->parse('self::$instance= null;'));
    }

    /**
     * Test assigning to a class member
     *
     */
    #[@test]
    public function toChain() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new ChainNode(array(
          0 => new ClassMemberNode(array(
            'class'     => new TypeName('self'), 
            'member'    => new VariableNode('instance'),
          )),
          1 => new InvocationNode(array('name' => 'addAppender', 'parameters' => NULL)),
          2 => new VariableNode('flags')
        )),
        'expression'    =>  new IntegerNode(array('value' => '0')),
        'op'            => '='
      ))), $this->parse('self::$instance.addAppender().flags= 0;'));
    }
  }
?>
