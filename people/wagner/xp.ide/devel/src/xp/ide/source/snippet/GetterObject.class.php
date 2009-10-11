<?php
/* This class is part of the XP framework
 *
 * $Id: ClassPathScanner.class.php 11282 2009-07-22 14:44:48Z ruben $ 
 */
  $package="xp.ide.source.snippet";

  uses(
    'xp.ide.source.element.ApidocDirective',
    'xp.ide.source.snippet.Getter'
  );

  /**
   * source representation
   * base object
   *
   * @purpose  IDE
   */
  class xp�ide�source�snippet�GetterObject extends xp�ide�source�snippet�Getter {

    protected function getApidicReturn($name, $type, $xtype, $dim) {
      return new xp�ide�source�element�ApidocDirective(sprintf('@return %s', $xtype));
    }

  }

?>
