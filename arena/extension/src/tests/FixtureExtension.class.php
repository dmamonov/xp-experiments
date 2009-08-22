<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'tests';
  
  uses('tests.Fixture');

  /**
   * Used by ExtensionMethodPerformance profiler
   *
   */
  class tests�FixtureExtension extends Object {

    static function __static() {
      xp::$registry['tests�Fixture::dec']= array(__CLASS__, 'dec');
    }

    /**
     * Extension method
     *
     * @param   tests.Fixture self
     * @param   int i
     */
    public static function dec(tests�Fixture $self, $i) {
      return $i--;
    }
  }
?>
