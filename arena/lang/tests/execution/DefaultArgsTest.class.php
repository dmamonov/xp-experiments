<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'tests.execution';

  uses('tests.execution.ExecutionTest');

  /**
   * Tests default arguments
   *
   */
  class tests�execution�DefaultArgsTest extends ExecutionTest {
    
    /**
     * Sets up test case and define class to be used in tests
     *
     */
    public function fixtureClass() {
      return $this->define('class', $this->name, NULL, '{
        public int[] $values;
        
        public __construct(int $a, int $b= 2) {
          $this.values= [$a, $b];
        }
      }');
    }
    
    /**
     * Test 
     *
     */
    #[@test]
    public function omitted() {
      $this->assertEquals(array(1, 2), $this->fixtureClass()->newInstance(1)->values);
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function passed() {
      $this->assertEquals(array(1, 2), $this->fixtureClass()->newInstance(1, 2)->values);
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function overridden() {
      $this->assertEquals(array(2, 3), $this->fixtureClass()->newInstance(2, 3)->values);
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function complex() {
      $class= $this->define('class', $this->name, NULL, '{
        public static Generic newInstance(XPClass $class= Object::class) {
          return $class.newInstance();
        }
      }');
      with ($i= $class->getMethod('newInstance')); {
        $this->assertClass($i->invoke(NULL, array()), 'lang.Object');
        $this->assertClass($i->invoke(NULL, array(XPClass::forName('util.Date'))), 'util.Date');
      }
    }
  }
?>