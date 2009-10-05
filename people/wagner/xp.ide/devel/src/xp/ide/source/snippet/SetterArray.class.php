<?php
/* This class is part of the XP framework
 *
 * $Id: ClassPathScanner.class.php 11282 2009-07-22 14:44:48Z ruben $ 
 */
  $package="xp.ide.source.snippet";

  uses(
    'xp.ide.source.element.Classmethodparam',
    'xp.ide.source.snippet.Setter'
  );

  /**
   * source representation
   * base object
   *
   * @purpose  IDE
   */
  class xp�ide�source�snippet�SetterArray extends xp�ide�source�snippet�Setter {
    protected function getParams($name, $type) {
      return array(new xp�ide�source�element�Classmethodparam($name, 'array'));
    }
  }

?>
