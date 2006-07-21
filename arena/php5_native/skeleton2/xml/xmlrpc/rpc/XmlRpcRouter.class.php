<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.xmlrpc.rpc.XmlRpcRequest',
    'xml.xmlrpc.rpc.XmlRpcResponse',
    'xml.xmlrpc.XmlRpcResponseMessage',
    'scriptlet.rpc.AbstractRpcRouter'
  );

  /**
   * XML-RPC Router class. You can use this class to implement
   * a XML-RPC webservice.
   *
   * <code>
   *   require('lang.base.php');
   *   xp::sapi('xmlrpc.service');
   * 
   *   $s= &new XmlRpcRouter(new ClassLoader('net.xp_framework.webservices.xmlrpc'));
   * 
   *   try(); {
   *     $s->init();
   *     $response= &$s->process();
   *   } if (catch('HttpScriptletException', $e)) {
   * 
   *     // Retrieve standard "Internal Server Error"-Document
   *     $response= &$e->getResponse();
   *   }
   * 
   *   $response->sendHeaders();
   *   $response->sendContent();
   * 
   *   $s->finalize();
   * </code>
   *
   * The default implementation of the XmlRpcRouter takes the given methodName from the
   * XML-RPC request, splits it at the '.' and takes the first part as the class name,
   * the second part as the method name. A request on a server with the setup given above
   * and a requested methodName of 'XmlRpcTest.runTest' would try to instanciate class
   * net.xp_framework.webservices.xmlrpc.XmlRpcTestHandler and run methon 'runTest'
   * on it.
   *
   * @ext      xml
   * @see      xp://xml.xmlrpc.XmlRpcClient
   * @purpose  XML-RPC-Service
   */
  class XmlRpcRouter extends AbstractRpcRouter {

    /**
     * Create a request object.
     *
     * @access  protected
     * @return  &xml.xmlrpc.rpc.XmlRpcRequest
     */
    public function &_request() {
      return new XmlRpcRequest();
    }

    /**
     * Create a response object.
     *
     * @access  protected
     * @return  &xml.xmlrpc.rpc.XmlRpcResponse
     */
    public function &_response() {
      return new XmlRpcResponse();
    }
    
    /**
     * Create a message object.
     *
     * @access  protected
     * @return  &xml.xmlrpc.XmlRpcResponseMessage
     */
    public function &_message() {
      return new XmlRpcResponseMessage();
    }
  }
?>
