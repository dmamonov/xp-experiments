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
  class LambdaTest extends ParserTestCase {

    /**
     * Test expression lambda
     *
     */
    #[@test]
    public function expression() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array(new ReturnNode(array('expression' => new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new IntegerNode(array('value' => '1')),
            'op'  => '+'
          )))))
        )), 
        $this->parse('{ $a => $a + 1 };')
      );
    }

    /**
     * Test statement lambda
     *
     */
    #[@test]
    public function statement() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array(new ReturnNode(array('expression' => new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new IntegerNode(array('value' => '1')),
            'op'  => '+'
          )))))
        )), 
        $this->parse('{ $a => { return $a + 1; } };')
      );
    }

    /**
     * Test statement lambda
     *
     */
    #[@test]
    public function multipleStatements() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array(
            new AssignmentNode(array(
              'variable'    => new VariableNode('a'),
              'expression'  => new IntegerNode(array('value' => '1')),
              'op'          => '+='
            )),
            new ReturnNode(array('expression' => new VariableNode('a')))
          )
        )), 
        $this->parse('{ $a => { $a+= 1; return $a; } };')
      );
    }

    /**
     * Test statement lambda
     *
     */
    #[@test]
    public function noStatements() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array()
        )), 
        $this->parse('{ $a => { } };')
      );
    }

    /**
     * Test parameter enclosed with brackets
     *
     */
    #[@test]
    public function typedParameterWithBrackets() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array(new ReturnNode(array('expression' => new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new IntegerNode(array('value' => '1')),
            'op'  => '+'
          )))))
        )), 
        $this->parse('{ int $a => { return $a + 1; } };')
      );
    }

    /**
     * Test parameters enclosed with brackets
     *
     */
    #[@test]
    public function parametersWithBrackets() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a'), new VariableNode('b')),
          array(new ReturnNode(array('expression' => new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new VariableNode('b'),
            'op'  => '+'
          )))))
        )), 
        $this->parse('{ $a, $b => { return $a + $b; } };')
      );
    }

    /**
     * Test parameters enclosed with brackets
     *
     */
    #[@test]
    public function typedParametersWithBrackets() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a'), new VariableNode('b')),
          array(new ReturnNode(array('expression' => new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new VariableNode('b'),
            'op'  => '+'
          )))))
        )), 
        $this->parse('{ int $a, int $b => { return $a + $b; } };')
      );
    }

    /**
     * Test parameters
     *
     */
    #[@test]
    public function emptyParameters() {
      $this->assertEquals(
        array(new LambdaNode(
          array(),
          array(new ReturnNode(array('expression' => new ClassMemberNode(array(
            'class'   => new TypeName('Console'),
            'member'  => new InvocationNode(array(
              'name'        => 'write',
              'parameters'  => array(
                new StringNode(array('value' => 'Hello'))
              )
            ))
          )))))
        )), 
        $this->parse('{ => Console::write("Hello") };')
      );
    }
  }
?>
