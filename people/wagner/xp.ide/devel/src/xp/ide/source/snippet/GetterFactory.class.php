<?php
/* This class is part of the XP framework
 *
 * $Id: ClassPathScanner.class.php 11282 2009-07-22 14:44:48Z ruben $ 
 */
  $package="xp.ide.source.snippet";

  uses(
    'xp.ide.source.snippet.Getter',
    'xp.ide.source.snippet.GetterBool'
  );

  /**
   * source representation
   * base object
   *
   * @purpose  IDE
   */
  class xp�ide�source�snippet�GetterFactory extends Object {
  
    public static function create($name, $type) {
      switch ($type) {
        case 'bool':
        case 'boolean':
        return new xp�ide�source�snippet�GetterBool($name, $type);

        default:
        return new xp�ide�source�snippet�Getter($name, $type);
      }
    }

  }

?>
