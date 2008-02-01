<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.SieveParserTestCase');

  /**
   * TestCase
   *
   * @see      xp://peer.sieve.ExistsCondition
   * @purpose  Unittest
   */
  class ExistsTest extends SieveParserTestCase {

    /**
     * Test
     *
     */
    #[@test]
    public function fromAndDateDoNotExist() {
      $condition= $this->parseRuleSetFrom('
        if not exists ["From","Date"] {
           discard;
        }
      ')->ruleAt(0)->condition->negated;
      $this->assertClass($condition, 'peer.sieve.ExistsCondition');
      $this->assertEquals(array('From', 'Date'), $condition->names);
    }
  }
?>
