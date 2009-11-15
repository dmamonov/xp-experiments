<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.NoSuchElementException', 'collections.AbstractDictionary');

  /**
   * Lookup map
   *
   */
  #[@generic(self= 'K, V', parent= 'K, V')]
  class Lookup extends AbstractDictionary {
    protected $elements= array();
    
    /**
     * Put a key/value pairt
     *
     * @param   K key
     * @param   V value
     */
    #[@generic(params= 'K, V')]
    public function put($key, $value) {
      $offset= $key instanceof Generic ? $key->hashCode() : $key;
      $this->elements[$offset]= $value;
    } 

    /**
     * Returns a value associated with a given key
     *
     * @param   K key
     * @return  V value
     * @throws  util.NoSuchElementException
     */
    #[@generic(params= 'K', return= 'V')]
    public function get($key) {
      $offset= $key instanceof Generic ? $key->hashCode() : $key;
      if (!isset($this->elements[$offset])) {
        throw new NoSuchElementException('No such key '.xp::stringOf($key));
      }
      return $this->elements[$offset];
    }

    /**
     * Returns all values
     *
     * @return  V[] values
     */
    #[@generic(return= 'V[]')]
    public function values() {
      return array_values($this->elements);
    }
  }
?>
