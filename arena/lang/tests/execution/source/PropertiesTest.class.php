<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'tests.execution.source';

  uses('tests.execution.source.ExecutionTest');

  /**
   * Tests properties
   *
   */
  class tests�execution�source�PropertiesTest extends ExecutionTest {
    protected $fixture= NULL;


    /**
     * Sets up test case
     *
     */
    public function setUp() {
      parent::setUp();
      try {
        $this->fixture= $this->define('class', 'StringBufferFor'.$this->name, NULL, '{
          protected string $buffer;

          public __construct(string $initial) {
            $this.buffer= $initial;
          }

          public int length {
            get { return strlen($this.buffer); }
            set { throw new lang.IllegalAccessException("Cannot set string length"); }
          }

          public string[] chars {
            get { return str_split($this.buffer); }
            set { $this.buffer= implode("", $value); }
          }

          public string this[int $offset] {
            get {
              return $offset >= 0 && $offset < $this.length ? $this.buffer[$offset] : null;
            }
            set {
              $this.buffer= substr($this.buffer, 0, $offset) ~ $value ~ substr($this.buffer, $offset+ 1);
            }
            unset {
              throw new lang.IllegalAccessException("Cannot remove string offsets");
            }
            isset {
              return $offset >= 0 && $offset < $this.length;
            }
          }

          public string toString() {
            return $this.buffer;
          }
        }', array(
          'import native core.strlen;', 
          'import native standard.str_split;',
          'import native standard.substr;',
          'import native standard.implode;',
        ));
      } catch (Throwable $e) {
        throw new PrerequisitesNotMetError($e->getMessage(), $e);
      }
    }
    
    /**
     * Test reading the length property
     *
     */
    #[@test]
    public function readLength() {
      $str= $this->fixture->newInstance('Hello');
      $this->assertEquals(5, $str->length);
    }

    /**
     * Test reading the chars property
     *
     */
    #[@test]
    public function readChars() {
      $str= $this->fixture->newInstance('Hello');
      $this->assertEquals(array('H', 'e', 'l', 'l', 'o'), $str->chars);
    }

    /**
     * Test writing the length property
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function writeLength() {
      $str= $this->fixture->newInstance('Hello');
      $str->length= 5;
    }

    /**
     * Test writing the chars property
     *
     */
    #[@test]
    public function writeChars() {
      $str= $this->fixture->newInstance('Hello');
      $str->chars= array('A', 'B', 'C');
      $this->assertEquals('ABC', $str->toString());
    }

    /**
     * Test reading offsets
     *
     */
    #[@test]
    public function offsetGet() {
      $str= $this->fixture->newInstance('Hello');
      $this->assertEquals('H', $str[0], 0);
      $this->assertEquals('o', $str[4], 4);
    }

    /**
     * Test writing to offsets
     *
     */
    #[@test]
    public function offsetSet() {
      $str= $this->fixture->newInstance('Hello');
      $str[1]= 'a';
      $this->assertEquals('Hallo', $str->toString());
    }

    /**
     * Test testing offsets
     *
     */
    #[@test]
    public function offsetExists() {
      $str= $this->fixture->newInstance('Hello');
      $this->assertTrue(isset($str[0]));
      $this->assertTrue(isset($str[4]));
      $this->assertFalse(isset($str[-1]));
      $this->assertFalse(isset($str[5]));
    }

    /**
     * Test removing offsets
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function offsetUnset() {
      $str= $this->fixture->newInstance('Hello');
      unset($str[0]);
    }

    /**
     * Test reading non-existant offsets
     *
     */
    #[@test]
    public function getNonExistantOffset() {
      $str= $this->fixture->newInstance('Hello');
      $this->assertEquals(NULL, $str[-1], -1);
      $this->assertEquals(NULL, $str[5], 5);
    }
  }
?>
