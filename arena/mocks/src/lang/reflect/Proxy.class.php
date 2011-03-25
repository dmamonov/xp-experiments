<?php
/* This class is part of the XP framework
 *
 * $Id: Proxy.class.php 14483 2010-04-17 14:30:29Z friebe $ 
 */

  uses('lang.reflect.IProxy',
       'lang.ClassLoader');

  define('PROXY_PREFIX',    'Proxy�');

  /**
   * Proxy provides static methods for creating dynamic proxy
   * classes and instances, and it is also the superclass of all
   * dynamic proxy classes created by those methods.
   *
   * @test     xp://net.xp_framework.unittest.reflection.ProxyTest
   * @purpose  Dynamically create classes
   * @see      http://java.sun.com/j2se/1.5.0/docs/api/java/lang/reflect/Proxy.html
   */
  class Proxy extends Object {   
    private
      $classLoader= null,
      $overwriteExisting= false,
      $added= array();

    private static 
      $num=0,
      $cache=array();
    
    /**
     * Constructor
     * 
     * @param lang.ClassLoader $classLoader
     */
    public function  __construct($classLoader=NULL) {
      if($classLoader==null)
        $this->classLoader= ClassLoader::getDefault();
      else
        $this->classLoader= $classLoader;
    }
    
    /**
     * Sets whether to overwrite existing implementations of concrete methods.
     * 
     * @param boolean value
     */
    public function setOverwriteExisting($value){
      $this->overwriteExisting=$value;
    }
       
    /**
     * Returns the XPClass object for a proxy class given a class loader 
     * and an array of interfaces.  The proxy class will be defined by the 
     * specified class loader and will implement all of the supplied 
     * interfaces (also loaded by the classloader).
     *
     * @param   lang.IClassLoader classloader
     * @param   lang.XPClass[] interfaces names of the interfaces to implement
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    public function createProxyClass(IClassLoader $classloader, array $interfaces, $baseClass=NULL) {
      $this->added= array();

      if (!$baseClass) {
        $baseClass = XPClass::forName('lang.Object');
      }
      //check if class is already in cache
      $key= $this->buildCacheId($baseClass, $interfaces);
      if(NULL !== ($cached=$this->tryGetFromCache($key))) {
        return $cached;
      }

      //write class definition
      //class <name> extends <baseClass> implements IProxy, <interfaces> {
      $bytes=$this->generateHead($baseClass, $interfaces);

      //add instance variables and constructor
      $bytes.=$this->generatePreamble();

      //generate code for (abstract) class methods (if any)
      $bytes.=$this->generateBaseClassMethods($baseClass);

      //generate code for interface methods
      for ($j= 0; $j < sizeof($interfaces); $j++) {
        $bytes.=$this->generateInterfaceMethods($interfaces[$j]);
      }
      //done.
      $bytes.= ' }';

      //create the actual class
      $class= $this->createClass($bytes);
      // Update cache+counter and return XPClass object
      self::$cache[$key]= $class;
      self::$num++;
      
      return $class;
    }

    /**
     * Generates the class header.
     * "class <name> extends <baseClass> implements IProxy, <interfaces> {"
     *
     * @param lang.XPClass baseClass
     * @param lang.XPClass[] interfaces
     * @return string 
     */
    private function generateHead($baseClass, $interfaces) {
      // Create proxy class' name, using a unique identifier and a prefix
      $name= $this->getProxyName();
      $bytes= 'class '.$name.' extends '.xp::reflect($baseClass->getName()).' implements IProxy, ';

      for ($j= 0; $j < sizeof($interfaces); $j++) {
        $bytes.= xp::reflect($interfaces[$j]->getName()).', ';
      }
      $bytes= substr($bytes, 0, -2)." {\n";

      return $bytes;
    }

    /**
     * Generates the name for the current proxy from a prefix and a counter.
     *
     * @return string
     */
    public function getProxyName() {
      return PROXY_PREFIX.(self::$num);
    }

    /**
     * Check if the class is already cached and returns it, otherwise returns null.
     *
     * @param string key
     * @return lang.XPClass
     */
    private function tryGetFromCache($key) {
      if(isset(self::$cache[$key])) 
        return self::$cache[$key];
      
      return null;
    }
    
    /**
     * Calculate cache key (composed of the names of all interfaces)
     * @param lang.XPClass baseClass
     * @param lang.XPClass[] interfaces
     * @return string
     */
    private function buildCacheId($baseClass, $interfaces) {
      $key= $this->classLoader->hashCode().':'.$baseClass->getName().';';
      $key.=implode(';', array_map(create_function('$i', 'return $i->getName();'), $interfaces));
      $key.=$this->overwriteExisting?'override':'';

      return $key;
    }

    /**
     * Returns the name of the handler variable;
     * @return string
     */
    private function getHandlerName() {
      return '_h';
    }

    /**
     * Generates code for the class preamble containing initializations of
     * variables and the constructor.
     *
     * @return string
     */
    private function generatePreamble() {
      $handlerName=$this->getHandlerName();
      
      $preamble='private $'.$handlerName.'=null;'."\n\n";
      $preamble.='public function __construct($handler) {'."\n".
                 '  $this->'.$handlerName.'=$handler;'."\n".
                 "}\n";

      return $preamble;
    }

    /**
     * Generates code for implementing all interface methods.
     * 
     * @param lang.XPClass if
     * @return string
     */
    private function generateInterfaceMethods($if) {
      $bytes='';
      // Verify that the Class object actually represents an interface
      if (!$if->isInterface()) {
        throw new IllegalArgumentException($if->getName().' is not an interface');
      }

      // Implement all the interface's methods
      foreach ($if->getMethods() as $m) {
         // Check for already declared methods, do not redeclare them
        if (isset($this->added[$m->getName()])) continue;
        $this->added[$m->getName()]= TRUE;
        $bytes.=$this->generateMethod($m);
      }
      return $bytes;
    }

    /**
     * Generates code for (re)implementation of the (abstract) class methods
     * of the base class.
     *
     * @param lang.XPClass baseClass
     */
    private function generateBaseClassMethods($baseClass) {
      $bytes='';

      $reservedMethods= XPClass::forName('lang.Generic')->getMethods();
      $reservedMethodNames= array_map(create_function('$i', 'return $i->getName();'), $reservedMethods);
      
      foreach($baseClass->getMethods() as $m) {
        if(in_array($m->getName(), $reservedMethodNames)) //do not overwrite reserved methods
          continue;
        
        if($this->overwriteExisting || ($m->getModifiers()&2) == 2) { //implement abstract methods
          // Check for already declared methods, do not redeclare them
          if (isset($this->added[$m->getName()])) continue;
          $this->added[$m->getName()]= TRUE;
          $bytes.=$this->generateMethod($m);
        }
      }
      return $bytes;
    }

    
    /**
     * Generates code for a method.
     * 
     * @param lang.reflect.Method method
     * @return string
     */
    private function generateMethod($method) {
      $bytes='';
      // Build signature and argument list
      if ($method->hasAnnotation('overloaded')) {
        $signatures= $method->getAnnotation('overloaded', 'signatures');
        $methodax= 0;
        $cases= array();
        foreach ($signatures as $signature) {
          $args= sizeof($signature);
          $methodax= max($methodax, $args- 1);
          if (isset($cases[$args])) continue;

          $cases[$args]= (
            'case '.$args.': '.
            'return $this->_h->invoke($this, \''.$method->getName(TRUE).'\', array('.
            ($args ? '$_'.implode(', $_', range(0, $args- 1)) : '').'));'
          );
        }

        // Create method
        $bytes.= (
          'public function '.$method->getName().'($_'.implode('= NULL, $_', range(0, $methodax)).'= NULL) { '.
          'switch (func_num_args()) {'.implode("\n", $cases).
          ' default: throw new IllegalArgumentException(\'Illegal number of arguments\'); }'.
          '}'."\n"
        );
      } else {
        $signature= $args= '';
        foreach ($method->getParameters() as $param) {
          $restriction= $param->getTypeRestriction();
          $signature.= ', '.($restriction ? xp::reflect($restriction->getName()) : '').' $'.$param->getName();
          $args.= ', $'.$param->getName();
          $param->isOptional() && $signature.= '= '.var_export($param->getDefaultValue(), TRUE);
        }
        $signature= substr($signature, 2);
        $args= substr($args, 2);

        // Create method
        $bytes.= (
          'public function '.$method->getName().'('.$signature.') { '.
          'return $this->_h->invoke($this, \''.$method->getName(TRUE).'\', array('.$args.')); '.
          '}'."\n"
        );
      }
      return $bytes;
    }

    /**
     * Creates an XPClass instance from the specified code using a
     * DynamicClassLoader.
     *
     * @param string bytes
     * @return lang.XPClass
     */
    private function createClass($bytes) {
      // Define the generated class
      try {
        $dyn= DynamicClassLoader::instanceFor(__METHOD__);
        $dyn->setClassBytes($this->getProxyName(), $bytes);
        $class= $dyn->loadClass($this->getProxyName());
      } catch (FormatException $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
      return $class;
    }
    
    /**
     * Returns an instance of a proxy class for the specified interfaces
     * that dispatches method invocations to the specified invocation
     * handler.
     *
     * @param   lang.ClassLoader classloader
     * @param   lang.XPClass[] interfaces
     * @param   lang.reflect.InvocationHandler handler
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    public function createProxyInstance($classloader, $interfaces, $handler) {
      return $this->createProxyClass($classloader, $interfaces)->newInstance($handler);
    }
    
    /**
     * @deprecated Use non-static getProxyClass instead
     * 
     * Retrieves a Proxy instance.
     *
     * @param   lang.IClassLoader classloader
     * @param   lang.XPClass[] interfaces names of the interfaces to implement
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    public static function getProxyClass(IClassLoader $classloader, array $interfaces) {
      $proxy= new Proxy();
      return $proxy->createProxyClass($classloader, $interfaces);
    }
    
    /**
     * @deprecated Use non-static createProxyInstance instead
     *
     * @param   lang.ClassLoader classloader
     * @param   lang.XPClass[] interfaces
     * @param   lang.reflect.InvocationHandler handler
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    public static function newProxyInstance($classloader, $interfaces, $handler) {
      $proxy= new Proxy();
      return $proxy->createProxyInstance($classloader, $interfaces, $handler);
    }
  }
?>