<?php
/* This class is part of the XP framework
 *
 * $Id: ClassPathScanner.class.php 11282 2009-07-22 14:44:48Z ruben $ 
 */
  $package="xp.ide.source.snippet";

  uses(
    'xp.ide.source.snippet.Getter',
    'xp.ide.source.snippet.GetterArray',
    'xp.ide.source.snippet.GetterObject'
  );

  /**
   * source representation
   * base object
   *
   * @purpose  IDE
   */
  class xp�ide�source�snippet�GetterFactory extends Object {
  
    public static function create($name, $type, $xtype, $dim) {
      switch ($type) {
        case 'array':
        return new xp�ide�source�snippet�GetterArray($name, $type, $xtype, $dim);

        case 'object':
        return new xp�ide�source�snippet�GetterObject($name, $type, $xtype, $dim);

        case 'boolean':
        case 'integer':
        case 'double':
        case 'string':
        return new xp�ide�source�snippet�Getter($name, $type, $xtype, $dim);

        default:
        throw new IllegalArgumentException($type.': unknowen type for getter');
      }
    }

  }

?>
