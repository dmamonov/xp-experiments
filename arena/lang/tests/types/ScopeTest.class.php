<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.ast.VariableNode',
    'xp.compiler.emit.oel.Emitter',
    'xp.compiler.types.TypeReflection',
    'xp.compiler.types.TaskScope',
    'xp.compiler.diagnostic.NullDiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.task.CompilationTask'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.types.Scope
   */
  class ScopeTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Sets up this testcase
     *
     */
    public function setUp() {
      $this->fixture= new TaskScope(new CompilationTask(
        new FileSource(new File(__FILE__), Syntax::forName('xp')),
        new NullDiagnosticListener(),
        new FileManager(),
        new xp�compiler�emit�oel�Emitter()
      ));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function arrayType() {
      $this->assertEquals(new TypeName('var[]'), $this->fixture->typeOf(new ArrayNode()));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function mapType() {
      $this->assertEquals(new TypeName('[var:var]'), $this->fixture->typeOf(new MapNode()));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function stringType() {
      $this->assertEquals(new TypeName('string'), $this->fixture->typeOf(new StringNode()));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function intType() {
      $this->assertEquals(new TypeName('int'), $this->fixture->typeOf(new IntegerNode()));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function hexType() {
      $this->assertEquals(new TypeName('int'), $this->fixture->typeOf(new HexNode()));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function decimalType() {
      $this->assertEquals(new TypeName('double'), $this->fixture->typeOf(new DecimalNode()));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function nullType() {
      $this->assertEquals(new TypeName('lang.Object'), $this->fixture->typeOf(new NullNode()));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function boolType() {
      $this->assertEquals(new TypeName('bool'), $this->fixture->typeOf(new BooleanNode()));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function typeOfAComparison() {
      $this->assertEquals(new TypeName('bool'), $this->fixture->typeOf(new ComparisonNode()));
    }

    /**
     * Test setType() and typeOf() methods
     *
     */
    #[@test]
    public function registeredType() {
      with ($v= new VariableNode('h'), $t= new TypeName('util.collections.HashTable')); {
        $this->fixture->setType($v, $t);
        $this->assertEquals($t, $this->fixture->typeOf($v));
      }
    }

    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function unknownType() {
      $this->assertEquals(TypeName::$VAR, $this->fixture->typeOf(new VariableNode('v')));
    }

    /**
     * Test extension method API
     *
     */
    #[@test]
    public function objectExtension() {
      with (
        $objectType= new TypeReflection(XPClass::forName('lang.Object')), 
        $classNameMethod= new xp�compiler�types�Method('getClassName')
      ); {
        $this->fixture->addExtension($objectType, $classNameMethod);
        $this->assertTrue($this->fixture->hasExtension($objectType, $classNameMethod->name));
        $this->assertEquals(
          $classNameMethod,
          $this->fixture->getExtension($objectType, $classNameMethod->name)
        );
      }
    }

    /**
     * Test extension method API
     *
     */
    #[@test]
    public function objectExtensionInherited() {
      with (
        $objectType= new TypeReflection(XPClass::forName('lang.Object')), 
        $dateType= new TypeReflection(XPClass::forName('util.Date')),
        $classNameMethod= new xp�compiler�types�Method('getClassName')
      ); {
        $this->fixture->addExtension($objectType, $classNameMethod);
        $this->assertTrue($this->fixture->hasExtension($dateType, $classNameMethod->name));
        $this->assertEquals(
          $classNameMethod,
          $this->fixture->getExtension($dateType, $classNameMethod->name)
        );
      }
    }

    /**
     * Test addTypeImport
     *
     */
    #[@test, @expect('xp.compiler.types.ResolveException')]
    public function importNonExistant() {
      $this->fixture->addTypeImport('util.cmd.@@NON_EXISTANT@@');
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveFullyQualified() {
      $this->assertEquals(
        new TypeReflection(XPClass::forName('util.cmd.Command')), 
        $this->fixture->resolveType(new TypeName('util.cmd.Command'))
      );
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveUnqualified() {
      $this->fixture->addTypeImport('util.cmd.Command');
      $this->assertEquals(
        new TypeReflection(XPClass::forName('util.cmd.Command')), 
        $this->fixture->resolveType(new TypeName('Command'))
      );
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveUnqualifiedByPackageImport() {
      $this->fixture->addPackageImport('util.cmd');
      $this->assertEquals(
        new TypeReflection(XPClass::forName('util.cmd.Command')), 
        $this->fixture->resolveType(new TypeName('Command'))
      );
    }

    /**
     * Test used list
     *
     */
    #[@test]
    public function usedAfterPackageImport() {
      $this->fixture->addPackageImport('util.cmd');
      $this->fixture->resolveType(new TypeName('Command'));
      
      $this->assertEquals(array(new TypeName('util.cmd.Command')), $this->fixture->used);
    }
  }
?>
