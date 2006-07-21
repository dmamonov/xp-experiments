<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.profiling.unittest.TestCase', 'lang.reflect.Proxy', 'lang.reflect.InvocationHandler');

  /**
   * Tests the Proxy class
   *
   * @see      xp://lang.reflect.Proxy
   * @purpose  Unit test
   */
  class ProxyTest extends TestCase {
    public
      $handler       = NULL,
      $iteratorClass = NULL,
      $observerClass = NULL;

    /**
     * Setup method 
     *
     * @access  public
     */
    public function setUp() {
      $class= &ClassLoader::defineClass(
        'net.xp_framework.unittest.reflection.DebugInvocationHandler', 
        'class DebugInvocationHandler extends Object implements InvocationHandler {
           var $invocations= array();

           function invoke(&$proxy, $method, $args) { 
             $this->invocations[$method]= $args;
           }
        }
        '
      );
      $this->handler= &$class->newInstance();
      $this->iteratorClass= &XPClass::forName('util.Iterator');
      $this->observerClass= &XPClass::forName('util.Observer');
    }

    /**
     * Helper method which returns a proxy instance for a given list of
     * interfaces, using the default classloader and the handler defined
     * in setUp()
     *
     * @access  protected
     * @param   lang.XPClass[] interfaces
     * @return  &lang.reflect.Proxy
     */
    public function &proxyInstanceFor($interfaces) {
      return Proxy::newProxyInstance(
        ClassLoader::getDefault(),
        $interfaces, 
        $this->handler
      );
    }
    
    /**
     * Helper method which returns a proxy class for a given list of
     * interfaces, using the default classloader and the handler defined
     * in setUp()
     *
     * @access  protected
     * @param   lang.XPClass[] interfaces
     * @return  &lang.XPClass
     */
    public function &proxyClassFor($interfaces) {
      return Proxy::getProxyClass(
        ClassLoader::getDefault(),
        $interfaces,
        $this->handler
      );
    }

    /**
     * Tests Proxy classes are prefixed to make them unique. The prefix
     * is a constant defined in the Proxy class.
     *
     * @access  public
     */
    #[@test]
    public function proxyClassNamesGetPrefixed() {
      $class= &$this->proxyClassFor(array($this->iteratorClass));
      $this->assertEquals(PROXY_PREFIX, substr($class->getName(), 0, strlen(PROXY_PREFIX)));
    }

    /**
     * Tests calling getProxyClass() twice with the same interface list
     * will result in the same proxy class
     *
     * @access  public
     */
    #[@test]
    public function classesEqualForSameInterfaceList() {
      $c1= &$this->proxyClassFor(array($this->iteratorClass));
      $c2= &$this->proxyClassFor(array($this->iteratorClass));
      $c3= &$this->proxyClassFor(array($this->iteratorClass, $this->observerClass));

      $this->assertEquals($c1, $c2);
      $this->assertNotEquals($c1, $c3);
    }

    /**
     * Tests Proxy implements the interface(s) passed
     *
     * @access  public
     */
    #[@test, @ignore]
    public function iteratorInterfaceIsImplemented() {
      $class= &$this->proxyClassFor(array($this->iteratorClass));
      $interfaces= $class->getInterfaces();
      $this->assertEquals(1, sizeof($interfaces));
      $this->assertEquals($this->iteratorClass, $interfaces[0]);
    }

    /**
     * Tests Proxy implements the interface(s) passed
     *
     * @access  public
     */
    #[@test, @ignore]
    public function allInterfacesAreImplemented() {
      $class= &$this->proxyClassFor(array($this->iteratorClass, $this->observerClass));
      $interfaces= $class->getInterfaces();
      $this->assertEquals(2, sizeof($interfaces));
      $this->assertIn($interfaces, $this->iteratorClass);
      $this->assertIn($interfaces, $this->observerClass);
    }

    /**
     * Tests Proxy implements all Iterator methods
     *
     * @access  public
     */
    #[@test, @ignore]
    public function iteratorMethods() {
      $expected= array(
        'hashcode', 'equals', 'getclassname', 'getclass', 'tostring', // lang.Object
        'getproxyclass', 'newproxyinstance',                          // lang.reflect.Proxy
        'hasnext', 'next'                                             // util.Iterator
      );
      
      $class= &$this->proxyClassFor(array($this->iteratorClass));
      $methods= $class->getMethods();

      $this->assertEquals(sizeof($expected), sizeof($methods));
      foreach ($methods as $method) {
        $this->assertTrue(
          in_array(strtolower($method->getName()), $expected), 
          'Unexpected method "'.$method->getName().'"'
        );
      }
    }

    /**
     * Tests util.Iterator::next() invocation without arguments
     *
     * @access  public
     */
    #[@test, @ignore]
    public function iteratorNextInvoked() {
      $proxy= &$this->proxyInstanceFor(array($this->iteratorClass));
      $proxy->next();
      $this->assertEquals(array(), $this->handler->invocations['next']);
    }
    
    /**
     * Tests proxies can not be created for classes, only for interfaces
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cannotCreateProxiesForClasses() {
      $this->proxyInstanceFor(array(XPClass::forName('lang.Object')));
    }
    
    /**
     * Check that implementing two interfaces that declare a common
     * method does not issue a fatal error.
     *
     * @access  public
     */
    #[@test, @ignore]
    public function allowDoubledInterfaceMethod() {
      $cl= &ClassLoader::getDefault();
      $newIteratorClass= &$cl->defineClass('util.NewIterator', 'interface NewIterator extends Iterator {
      }');
      
      $this->proxyInstanceFor(array(
        XPClass::forName('util.Iterator'),
        XPClass::forName('util.NewIterator')
      ));
    }
  }
?>
