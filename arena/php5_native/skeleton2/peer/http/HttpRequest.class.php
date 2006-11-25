<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
    'peer.http.HttpConstants',
    'peer.Socket',
    'peer.URL',
    'peer.http.HttpResponse',
    'peer.http.RequestData',
    'peer.Header'
  );
  
  /**
   * Wrap HTTP/1.0 and HTTP/1.1 requests (used internally by the 
   * HttpConnection class)
   *
   * @see      xp://peer.http.HttpConnection
   * @see      rfc://2616
   * @purpose  HTTP request
   */
  class HttpRequest extends Object {
    public
      $url        = NULL,
      $method     = HTTP_GET,
      $version    = HTTP_VERSION_1_1,
      $headers    = array(
        'Connection' => 'close'
      ),
      $parameters = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &peer.URL url object
     */
    public function __construct(&$url) {
      $this->url= &$url;
      if ($url->getUser() && $url->getPassword()) {
        $this->headers['Authorization']= 'Basic '.base64_encode($url->getUser().':'.$url->getPassword());
      }
      $this->headers['Host']= $this->url->getHost().':'.$this->url->getPort(80);
    }
    
    /**
     * Set request method
     *
     * @access  public
     * @param   string method request method, e.g. HTTP_GET
     */
    public function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Set request parameters
     *
     * @access  public
     * @param   mixed p either a string, a PostData object or an associative array
     */
    public function setParameters($p) {
      if (is('RequestData', $p)) {
        $this->parameters= &$p;
      } else if (is_string($p)) {
        parse_str($p, $this->parameters); 
      } else {
        $this->parameters= array_merge($this->url->getParams(), $p);
      }
    }
    
    /**
     * Set header
     *
     * @access  public
     * @param   string k header name
     * @param   string v header value
     */
    public function setHeader($k, $v) {
      $this->headers[$k]= $v;
    }

    /**
     * Add headers
     *
     * @access  public
     * @param   array headers
     */
    public function addHeaders($headers) {
      foreach ($headers as $key => $header) {
        $this->headers[is('Header', $header) ? $header->getName() : $key] = $header;
      }
    }
    
    /**
     * Get request string
     *
     * @access  public
     * @return  string
     */
    public function getRequestString() {
      if (is('RequestData', $this->parameters)) {
        $query= "\0".$this->parameters->getData();
      } else {
        $query= '';
        if (is_array($this->parameters)) foreach ($this->parameters as $k => $v) {
          $query.= '&'.$k.'='.urlencode($v);
        }
      }
      $target= $this->url->getPath('/');
      
      // Which HTTP method? GET and HEAD use query string, POST etc. use
      // body for passing parameters
      switch ($this->method) {
        case HTTP_HEAD:
        case HTTP_GET:
          $target.= empty($query) ? '' : '?'.substr($query, 1);
          $body= '';
          break;
          
        case HTTP_POST:
        default:
          $body= substr($query, 1);
          if (NULL !== $this->url->getQuery()) $target.= '?'.$this->url->getQuery();
          $this->headers['Content-Length']= strlen($body);
          if (empty($this->headers['Content-Type'])) {
            $this->headers['Content-Type']= 'application/x-www-form-urlencoded';
          }
          break;
      }
      
      $request= sprintf(
        "%s %s HTTP/%s\r\n",
        $this->method,
        $target,
        $this->version
      );
      
      // Add request headers
      foreach ($this->headers as $k => $v) {
        $request.= (is('Header', $v) 
          ? $v->toString() 
          : $k.': '.$v
        )."\r\n";
      }
      
      return $request."\r\n".$body;
    }
    
    /**
     * Send request
     *
     * @access  public
     * @return  &peer.http.HttpResponse response object
     */
    public function &send($timeout= 60) {
      $s= new Socket($this->url->getHost(), $this->url->getPort(80));
      $s->setTimeout($timeout);
      
      $request= $this->getRequestString();
      try {
        $s->connect() && $s->write($request);
      } catch (Exception $e) {
        throw($e);
      }
      
      return new HttpResponse($s);
    }
  }
?>
