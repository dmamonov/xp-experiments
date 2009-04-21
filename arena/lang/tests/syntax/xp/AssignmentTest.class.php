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
        'position'      => array(4, 14),
        'variable'      => $this->create(new VariableNode('i'), array(4, 9)),
        'expression'    => new IntegerNode(array('position' => array(4, 13), 'value' => '0')),
        'op'            => '='
      ))), $this->parse('
        $i= 0;
      '));
    }

    /**
     * Test assigning to a variable with array offset
     *
     */
    #[@test]
    public function toArrayOffset() {
      $this->assertEquals(array(new AssignmentNode(array(
        'position'      => array(4, 17),
        'variable'      => $this->create(new ChainNode(array(
          0 => $this->create(new VariableNode('i'), array(4, 9)),
          1 => $this->create(new ArrayAccessNode(array(
            'offset' => $this->create(new IntegerNode(array('value' => '0')), array(4, 12))
          )), array(4, 11)),
        )), array(4, 14)),
        'expression'    => new IntegerNode(array('position' => array(4, 16), 'value' => '0')),
        'op'            => '='
      ))), $this->parse('
        $i[0]= 0;
      '));
    }

    /**
     * Test assigning to a variable with array offset
     *
     */
    #[@test]
    public function appendToArray() {
      $this->assertEquals(array(new AssignmentNode(array(
        'position'      => array(4, 16),
        'variable'      => $this->create(new ChainNode(array(
          0 => $this->create(new VariableNode('i'), array(4, 9)),
          1 => $this->create(new ArrayAccessNode(array('offset' => NULL)), array(4, 11)),
        )), array(4, 13)),
        'expression'    => new IntegerNode(array('position' => array(4, 15), 'value' => '0')),
        'op'            => '='
      ))), $this->parse('
        $i[]= 0;
      '));
    }

    /**
     * Test assigning to an instance member
     *
     */
    #[@test]
    public function toInstanceMember() {
      $this->assertEquals(array(new AssignmentNode(array(
        'position'      => array(4, 25),
        'variable'      => $this->create(new ChainNode(array(
          0 => $this->create(new VariableNode('class'), array(4, 9)),
          1 => $this->create(new VariableNode('member'), array(4, 22))
        )), array(4, 22)),
        'expression'    => new IntegerNode(array('position' => array(4, 24), 'value' => '0')),
        'op'            => '='
      ))), $this->parse('
        $class.member= 0;
      '));
    }

    /**
     * Test assigning to a class member
     *
     */
    #[@test]
    public function toClassMember() {
      $this->assertEquals(array(new AssignmentNode(array(
        'position'      => array(4, 30),
        'variable'      => new ClassMemberNode(array(
          'position'      => array(4, 13), 
          'class'         => new TypeName('self'),
          'member'        => $this->create(new VariableNode('instance'), array(4, 15))
        )),
        'expression'    => $this->create(new NullNode(), array(4, 30)),
        'op'            => '='
      ))), $this->parse('
        self::$instance= null;
      '));
    }

    /**
     * Test assigning to a class member
     *
     */
    #[@test]
    public function toChain() {
      $this->assertEquals(array(new AssignmentNode(array(
        'position'      => array(4, 47),
        'variable'      => $this->create(new ChainNode(array(
          0 => $this->create(new ClassMemberNode(array(
            'class'     => new TypeName('self'), 
            'member'    => $this->create(new VariableNode('instance'), array(4, 15))
          )), array(4, 13)),
          1 => $this->create(new InvocationNode(array('name' => 'addAppender', 'parameters' => NULL)), array(4, 36)),
          2 => $this->create(new VariableNode('flags'), array(4, 44))
        )), array(4, 44)),
        'expression'    =>  new IntegerNode(array('position' => array(4, 46), 'value' => '0')),
        'op'            => '='
      ))), $this->parse('
        self::$instance.addAppender().flags= 0;
      '));
    }
  }
?>
