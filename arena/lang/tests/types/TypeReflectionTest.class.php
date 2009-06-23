<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.emit.TypeReflection'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.emit.TypeReflection
   */
  class TypeReflectionTest extends TestCase {
  
    /**
     * Test name() method
     *
     */
    #[@test]
    public function name() {
      $decl= new TypeReflection(XPClass::forName('unittest.TestCase'));
      $this->assertEquals('unittest.TestCase', $decl->name());
    }

    /**
     * Test literal() method
     *
     */
    #[@test]
    public function literal() {
      $decl= new TypeReflection(XPClass::forName('unittest.TestCase'));
      $this->assertEquals('TestCase', $decl->literal());
    }

    /**
     * Test hasMethod() method
     *
     */
    #[@test]
    public function objectClassHasMethod() {
      $decl= new TypeReflection(XPClass::forName('lang.Object'));
      $this->assertTrue($decl->hasMethod('equals'), 'equals');
      $this->assertFalse($decl->hasMethod('getName'), 'getName');
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function classKind() {
      $decl= new TypeReflection(XPClass::forName('lang.Object'));
      $this->assertEquals(Types::CLASS_KIND, $decl->kind());
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function interfaceKind() {
      $decl= new TypeReflection(XPClass::forName('lang.Generic'));
      $this->assertEquals(Types::INTERFACE_KIND, $decl->kind());
    }

    /**
     * Test hasMethod() method
     *
     */
    #[@test]
    public function stringClassHasMethod() {
      $decl= new TypeReflection(XPClass::forName('lang.types.String'));
      $this->assertTrue($decl->hasMethod('equals'), 'equals');
      $this->assertTrue($decl->hasMethod('substring'), 'substring');
      $this->assertFalse($decl->hasMethod('getName'), 'getName');
    }
  }
?>