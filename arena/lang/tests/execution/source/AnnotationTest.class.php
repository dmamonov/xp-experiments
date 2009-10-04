<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'tests.execution.source';

  uses('tests.execution.source.ExecutionTest');

  /**
   * Tests annotations
   *
   */
  class tests�execution�source�AnnotationTest extends ExecutionTest {
    protected $fixture= NULL;
    
    /**
     * Sets up test case and define class to be used in fixtures
     *
     */
    public function setUp() {
      $this->fixture= $this->define('class', 'AnnotationsFor'.$this->name, NULL, '{
      
        [@test]
        public function getAll() { }
        
        [@test, @ignore("Risky")]
        public function deleteAll() { }

        [@test, @limit(time = 0.1)]
        public function updateAll() { }

        // TODO: Support this grammatically
        //
        // [@test, @expect(lang.FormatException::class)]
        // public function findBy() { }

        [@restricted(roles = ["admin", "root"])]
        public function reset() { }
      }');
    }
    
    /**
     * Test simple annotation
     *
     */
    #[@test]
    public function testAnnotation() {
      with ($m= $this->fixture->getMethod('getAll')); {
        $this->assertTrue($m->hasAnnotation('test'));
        $this->assertEquals(NULL, $m->getAnnotation('test'));
      }
    }

    /**
     * Test multiple annotations
     *
     */
    #[@test]
    public function ignoreAnnotation() {
      with ($m= $this->fixture->getMethod('deleteAll')); {
        $this->assertTrue($m->hasAnnotation('test'), '@test');
        $this->assertEquals(NULL, $m->getAnnotation('test'), '@test');
        $this->assertTrue($m->hasAnnotation('ignore'), '@ignore');
        $this->assertEquals('Risky', $m->getAnnotation('ignore'), '@ignore');
      }
    }

    /**
     * Test multiple annotations
     *
     */
    #[@test]
    public function limitAnnotation() {
      with ($m= $this->fixture->getMethod('updateAll')); {
        $this->assertTrue($m->hasAnnotation('test'), '@test');
        $this->assertEquals(NULL, $m->getAnnotation('test'), '@test');
        $this->assertTrue($m->hasAnnotation('limit'), '@limit');
        $this->assertEquals(array('time' => 0.1), $m->getAnnotation('limit'), '@limit');
      }
    }

    /**
     * Test annotation with array value
     *
     */
    #[@test]
    public function restrictedAnnotation() {
      with ($m= $this->fixture->getMethod('reset')); {
        $this->assertTrue($m->hasAnnotation('restricted'));
        $this->assertEquals(array('roles' => array('admin', 'root')), $m->getAnnotation('restricted'));
      }
    }
  }
?>
