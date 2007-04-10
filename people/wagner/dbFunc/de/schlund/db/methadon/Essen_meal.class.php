<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table essen_meal, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Essen_meal extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..essen_meal');
        $peer->setConnection('sybintern');
        $peer->setIdentity('meal_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'count_offered'       => array('%d', FieldType::INT, FALSE),
          'meal_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'meal_type_id'        => array('%d', FieldType::NUMERIC, FALSE),
          'price_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE)
        ));
      }
    }  

    function __get($name) {
      $this->load();
      return $this->get($name);
    }

    function __sleep() {
      $this->load();
      return array_merge(array_keys(self::getPeer()->types), array('_new', '_changed'));
    }

    /**
     * force loading this entity from database
     *
     */
    public function load() {
      if ($this->_isLoaded) return;
      $this->_isLoaded= true;
      $e= self::getPeer()->doSelect($this->_loadCrit);
      if (!$e) return;
      foreach (array_keys(self::getPeer()->types) as $p) {
        if (isset($this->{$p})) continue;
        $this->{$p}= $e[0]->$p;
      }
    }

    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Retrieves name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
      
    /**
     * Sets name
     *
     * @param   string name
     * @return  string the previous value
     */
    public function setName($name) {
      return $this->_change('name', $name);
    }

    /**
     * Retrieves description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }
      
    /**
     * Sets description
     *
     * @param   string description
     * @return  string the previous value
     */
    public function setDescription($description) {
      return $this->_change('description', $description);
    }

    /**
     * Retrieves changedby
     *
     * @return  string
     */
    public function getChangedby() {
      return $this->changedby;
    }
      
    /**
     * Sets changedby
     *
     * @param   string changedby
     * @return  string the previous value
     */
    public function setChangedby($changedby) {
      return $this->_change('changedby', $changedby);
    }

    /**
     * Retrieves count_offered
     *
     * @return  int
     */
    public function getCount_offered() {
      return $this->count_offered;
    }
      
    /**
     * Sets count_offered
     *
     * @param   int count_offered
     * @return  int the previous value
     */
    public function setCount_offered($count_offered) {
      return $this->_change('count_offered', $count_offered);
    }

    /**
     * Retrieves meal_id
     *
     * @return  int
     */
    public function getMeal_id() {
      return $this->meal_id;
    }
      
    /**
     * Sets meal_id
     *
     * @param   int meal_id
     * @return  int the previous value
     */
    public function setMeal_id($meal_id) {
      return $this->_change('meal_id', $meal_id);
    }

    /**
     * Retrieves meal_type_id
     *
     * @return  int
     */
    public function getMeal_type_id() {
      return $this->meal_type_id;
    }
      
    /**
     * Sets meal_type_id
     *
     * @param   int meal_type_id
     * @return  int the previous value
     */
    public function setMeal_type_id($meal_type_id) {
      return $this->_change('meal_type_id', $meal_type_id);
    }

    /**
     * Retrieves price_id
     *
     * @return  int
     */
    public function getPrice_id() {
      return $this->price_id;
    }
      
    /**
     * Sets price_id
     *
     * @param   int price_id
     * @return  int the previous value
     */
    public function setPrice_id($price_id) {
      return $this->_change('price_id', $price_id);
    }

    /**
     * Retrieves bz_id
     *
     * @return  int
     */
    public function getBz_id() {
      return $this->bz_id;
    }
      
    /**
     * Sets bz_id
     *
     * @param   int bz_id
     * @return  int the previous value
     */
    public function setBz_id($bz_id) {
      return $this->_change('bz_id', $bz_id);
    }

    /**
     * Retrieves lastchange
     *
     * @return  util.Date
     */
    public function getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @param   util.Date lastchange
     * @return  util.Date the previous value
     */
    public function setLastchange($lastchange) {
      return $this->_change('lastchange', $lastchange);
    }
  }
?>