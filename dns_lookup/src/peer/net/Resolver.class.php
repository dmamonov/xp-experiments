<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'peer.net';

  uses(
    'peer.net.ResolveException',
    'peer.net.Message',
    'peer.net.ARecord',
    'peer.net.CNAMERecord',
    'peer.net.MXRecord',
    'peer.net.NSRecord',
    'peer.net.AAAARecord',
    'peer.net.TXTRecord',
    'peer.net.SRVRecord',
    'peer.net.PTRRecord',
    'peer.net.NAPTRRecord',
    'peer.net.SOARecord'
  );

  /**
   * Resolver
   *
   */
  interface peer�net�Resolver {
    
    /**
     * Send query for resolution and return nameservers records
     *
     * @param   peer.net.Message query
     * @return  peer.net.Message The response
     * @throws  peer.net.ResolveException
     * @throws  lang.Throwable
     */
    public function send(peer�net�Message $query);
  }
?>