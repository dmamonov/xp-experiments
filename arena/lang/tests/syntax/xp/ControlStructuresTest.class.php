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
  class ControlStructuresTest extends ParserTestCase {
  
    /**
     * Test if statement without else
     *
     */
    #[@test]
    public function ifStatement() {
      $this->assertEquals(array(new IfNode(array(
        'condition'      => new VariableNode('i'),
        'statements'     => NULL,
        'otherwise'      => NULL, 
      ))), $this->parse('
        if ($i) { }
      '));
    }

    /**
     * Test if statement without curly braces
     *
     */
    #[@test]
    public function ifStatementWithOutCurlies() {
      $this->assertEquals(array(new IfNode(array(
        'condition'      => new VariableNode('i'),
        'statements'     => array(new ReturnNode(array(
          'expression'     => new BooleanNode(TRUE),
        ))),
        'otherwise'      => NULL, 
      ))), $this->parse('
        if ($i) return true;
      '));
    }

    /**
     * Test if statement with else
     *
     */
    #[@test]
    public function ifElseStatement() {
      $this->assertEquals(array(new IfNode(array(
        'condition'      => new VariableNode('i'),
        'statements'     => NULL, 
        'otherwise'      => new ElseNode(array(
          'statements'     => NULL,
        )), 
      ))), $this->parse('
        if ($i) { } else { }
      '));
    }

    /**
     * Test if /else cascades
     *
     */
    #[@test]
    public function ifElseCascades() {
      $this->assertEquals(array(new IfNode(array(
        'condition'      => new BinaryOpNode(array(
          'lhs'            => new VariableNode('i'),
          'rhs'            => new IntegerNode(array('value' => '3')),
          'op'             => '%'
        )),
        'statements'     => NULL, 
        'otherwise'      => new ElseNode(array(
          'statements'     => array(new IfNode(array(
            'condition'      => new BinaryOpNode(array(
              'lhs'            => new VariableNode('i'),
              'rhs'            => new IntegerNode(array('value' => '2')),
              'op'             => '%'
            )),
            'statements'     => NULL, 
            'otherwise'      => new ElseNode(array(
              'statements'     => NULL,
            )), 
          ))),
        )), 
      ))), $this->parse('
        if ($i % 3) { } else if ($i % 2) { } else { }
      '));
    }

    /**
     * Test switch statement
     *
     */
    #[@test]
    public function emptySwitchStatement() {
      $this->assertEquals(array(new SwitchNode(array(
        'expression'     => new VariableNode('i'),
        'cases'          => NULL,
      ))), $this->parse('
        switch ($i) { }
      '));
    }

    /**
     * Test switch statement
     *
     */
    #[@test]
    public function switchStatement() {
      $this->assertEquals(array(new SwitchNode(array(
        'expression'     => new VariableNode('i'),
        'cases'          => array(
          new CaseNode(array(
            'expression'     => new IntegerNode(array('value' => '0')),
            'statements'     => array(
              new StringNode(array('value' => 'no entries')),
              new BreakNode()
            )
          )),
          new CaseNode(array(
            'expression'     => new IntegerNode(array('value' => '1')),
            'statements'     => array(
              new StringNode(array('value' => 'one entry')),
              new BreakNode()
            )
         )),
          new DefaultNode(array(
            'statements'     => array(
              new BinaryOpNode(array(
                'lhs'        => new VariableNode('i'),
                'rhs'        => new StringNode(array('value' => ' entries')),
                'op'         => '~'
              )),
              new BreakNode()
            )
          ))
        ),
      ))), $this->parse('
        switch ($i) { 
          case 0: "no entries"; break;
          case 1: "one entry"; break;
          default: $i ~ " entries"; break;
        }
      '));
    }
  }
?>
