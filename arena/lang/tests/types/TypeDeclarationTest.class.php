<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.emit.TypeDeclaration'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.emit.TypeDeclaration
   */
  class TypeDeclarationTest extends TestCase {
  
    /**
     * Test name() method
     *
     */
    #[@test]
    public function nameWithoutPackage() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('TestCase')
      )));
      $this->assertEquals('TestCase', $decl->name());
    }

    /**
     * Test name() method
     *
     */
    #[@test]
    public function nameWithPackage() {
      $decl= new TypeDeclaration(new ParseTree(new TypeName('unittest.web'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('WebTestCase')
      )));
      $this->assertEquals('unittest.web.WebTestCase', $decl->name());
    }

    /**
     * Test literal() method
     *
     */
    #[@test]
    public function literalWithoutPackage() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('TestCase')
      )));
      $this->assertEquals('TestCase', $decl->literal());
    }

    /**
     * Test literal() method
     *
     */
    #[@test]
    public function literalWithPackage() {
      $decl= new TypeDeclaration(new ParseTree(new TypeName('unittest.web'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('WebTestCase')
      )));
      $this->assertEquals('WebTestCase', $decl->literal());
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function classKind() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('TestCase')
      )));
      $this->assertEquals(Types::CLASS_KIND, $decl->kind());
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function interfaceKind() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new InterfaceNode(array(
        'name' => new TypeName('Resolveable')
      ))));
      $this->assertEquals(Types::INTERFACE_KIND, $decl->kind());
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function enumKind() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new EnumNode(array(
        'name' => new TypeName('Operation')
      ))));
      $this->assertEquals(Types::ENUM_KIND, $decl->kind());
    }
    

    /**
     * Returns a type declaration for the string class
     *
     * @return  xp.compiler.emit.TypeDeclaration
     */
    protected function stringClass() {
      return new TypeDeclaration(
        new ParseTree(new TypeName('lang.types'), array(), new ClassNode(
          MODIFIER_PUBLIC, 
          NULL,
          new TypeName('String'),
          new TypeName('lang.Object'),
          NULL,
          array(
            'methods' => array(
              new MethodNode(array(
                'name' => 'substring'
              ))
            )
          )
        )),
        $this->objectClass()
      );
    }

    /**
     * Returns a type declaration for the object class
     *
     * @return  xp.compiler.emit.TypeDeclaration
     */
    protected function objectClass() {
      return new TypeDeclaration(
        new ParseTree(new TypeName('lang'), array(), new ClassNode(
          MODIFIER_PUBLIC, 
          NULL,
          new TypeName('Object'),
          NULL,
          NULL,
          array(
            'methods' => array(
              new MethodNode(array(
                'name' => 'equals'
              ))
            )
          )
        ))
      );
    }

    /**
     * Test hasMethod() method
     *
     */
    #[@test]
    public function objectClassHasMethod() {
      $decl= $this->objectClass();
      $this->assertTrue($decl->hasMethod('equals'), 'equals');
      $this->assertFalse($decl->hasMethod('getName'), 'getName');
    }

    /**
     * Test hasMethod() method
     *
     */
    #[@test]
    public function stringClassHasMethod() {
      $decl= $this->stringClass();
      $this->assertTrue($decl->hasMethod('equals'), 'equals');
      $this->assertTrue($decl->hasMethod('substring'), 'substring');
      $this->assertFalse($decl->hasMethod('getName'), 'getName');
    }
  }
?>
