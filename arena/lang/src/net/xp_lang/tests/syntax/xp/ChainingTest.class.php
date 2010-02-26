<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class ChainingTest extends ParserTestCase {
  
    /**
     * Test simple method call on an object
     *
     */
    #[@test]
    public function methodCall() {
      $this->assertEquals(
        array(new ChainNode(array(
          0 => new VariableNode('m'),
          1 => new InvocationNode('invoke', array(new VariableNode('args')))
        ))), 
        $this->parse('$m.invoke($args);')
      );
    }

    /**
     * Test chained method calls
     *
     */
    #[@test]
    public function chainedMethodCalls() {
      $this->assertEquals(
        array(new ChainNode(array(
          0 => new VariableNode('l'),
          1 => new InvocationNode('withAppender', array()),
          2 => new InvocationNode('debug', array())
        ))),
        $this->parse('$l.withAppender().debug();')
      );
    }

    /**
     * Test chained method calls
     *
     */
    #[@test]
    public function chainedAfterNew() {
      $this->assertEquals(
        array(new ChainNode(array(
          0 => new InstanceCreationNode(array(
            'type'           => new TypeName('Date'),
            'parameters'     => NULL,
          )),
          1 => new InvocationNode('toString', array())
        ))), 
        $this->parse('new Date().toString();')
      );
    }

    /**
     * Test chained method calls
     *
     */
    #[@test]
    public function arrayOffsetOnMethod() {
      $this->assertEquals(
        array(new ChainNode(array(
          0 => new VariableNode('l'),
          1 => new InvocationNode('elements', array()),
          2 => new ArrayAccessNode(new IntegerNode('0')),
          3 => new VariableNode('name'),
        ))),
        $this->parse('$l.elements()[0].name;')
      );
    }

    /**
     * Test chained method calls
     *
     */
    #[@test]
    public function chainedAfterStaticMethod() {
      $this->assertEquals(
        array(new ChainNode(array(
          0 => new ClassMemberNode(new TypeName('Logger'), new InvocationNode('getInstance', array())),
          1 => new InvocationNode('configure', array(new StringNode('etc')))
        ))), 
        $this->parse('Logger::getInstance().configure("etc");')
      );
    }

    /**
     * Test chaining after function calls
     *
     */
    #[@test]
    public function chainedAfterFunction() {
      $this->assertEquals(
        array(new ChainNode(array(
          0 => new InvocationNode('create', array(new VariableNode('a'))),
          1 => new InvocationNode('equals', array(new VariableNode('b')))
        ))), 
        $this->parse('create($a).equals($b);')
      );
    }

    /**
     * Test chained after bracing
     *
     */
    #[@test]
    public function chainedAfterBraced() {
      $this->assertEquals(
        array(new ChainNode(array(
          0 => new BracedExpressionNode(new CastNode(array(
            'type'       => new TypeName('Generic'),
            'expression' => new VariableNode('a')
          ))),
          1 => new InvocationNode('equals', array(new VariableNode('b')))
        ))), 
        $this->parse('($a as Generic).equals($b);')
      );
    }
  }
?>