<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'tests.execution.oel';

  uses('tests.execution.oel.ExecutionTest', 'lang.Enum');

  /**
   * Tests class declarations
   *
   */
  class tests�execution�oel�ExtensionMethodsTest extends tests�execution�oel�ExecutionTest {

    /**
     * Test extending
     *
     */
    #[@test]
    public function sorted() {
      $class= $this->define('class', 'ClassExtension', NULL, '{
        public static lang.reflect.Method[] methodsNamed(this lang.XPClass $class, text.regex.Pattern $pattern) {
          $r= new lang.reflect.Method[] { };
          foreach ($method in $class.getMethods()) {
            if ($pattern.matches($method.getName())) $r[]= $method;
          }
          return $r;
        }
        
        public lang.reflect.Method runMethod() {
          return self::class.methodsNamed(text.regex.Pattern::compile("run"))[0];
        }
      }');
      $this->assertEquals(
        $class->getMethod('runMethod'), 
        $class->newInstance()->runMethod()
      );
    }
  }
?>