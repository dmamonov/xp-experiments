<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.net.dns.Resolver');

  /**
   * Resolver that queries a list of resolvers
   *
   * @test    xp://net.xp_framework.unittest.peer.net.CompositeResolverTest
   */
  class CompositeResolver extends Object implements peer�net�dns�Resolver {
    protected $delegates= array();
    
    /**
     * Create a composite resolver
     *
     * @param   peer.net.Resolver[] resolvers
     */
    public function __construct($resolvers= array()) {
      $this->delegates= $resolvers;
    }

    /**
     * Add a resolver delegate to this composite
     *
     * @param   peer.net.dns.Resolver resolver
     * @return  peer.net.dns.Resolver the added resolver
     */
    public function addDelegate(peer�net�dns�Resolver $resolver) {
      $this->delegates[]= $resolver;
      return $resolver;
    }

    /**
     * Add a resolver delegate to this composite
     *
     * @param   peer.net.dns.Resolver resolver
     * @return  peer.net.dns.CompositeResolver this composite resolver
     */
    public function withDelegate(peer�net�dns�Resolver $resolver) {
      $this->delegates[]= $resolver;
      return $this;
    }

    /**
     * Get whether resolver delegates exists
     *
     * @return  bool
     */
    public function hasDelegates() {
      return !empty($this->delegates);
    }

    /**
     * Get all resolver delegates
     *
     * @return  peer.net.dns.Resolver[]
     */
    public function getDelegates() {
      return $this->delegates;
    }
    
    /**
     * Send query for resolution and return nameservers records
     *
     * @param   peer.net.dns.Message query
     * @return  peer.net.dns.Record response
     */
    public function send(peer�net�dns�Message $query) {
      if (empty($this->delegates)) {
        throw new IllegalStateException('No resolvers to query');
      }

      $t= NULL;
      foreach ($this->delegates as $resolver) {
        try {
          return $resolver->send($query);
        } catch (Throwable $t) {
          continue;
        }
      }
      throw $t;
    }

    /**
     * Set domain
     *
     * @param   string name
     */
    public function setDomain($name) {
      foreach ($this->delegates as $resolver) {
        $resolver->setDomain($name);
      }
    }

    /**
     * Set search list
     *
     * @param   string[] domains
     */
    public function setSearch($domains) {
      foreach ($this->delegates as $resolver) {
        $resolver->setSearch($domains);
      }
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName()."@{\n";
      foreach ($this->delegates as $i => $resolver) {
        $s.= '  '.str_replace("\n", "\n  ", $resolver->toString())."\n";
      }
      return $s.'}';
    }
  }
?>
