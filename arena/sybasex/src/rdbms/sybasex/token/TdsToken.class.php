  <?php
  uses('util.log.Traceable');
  
  $package= 'rdbms.sybasex.token';
  abstract class rdbms�sybasex�token�TdsToken extends Object implements Traceable {
    protected
      $data     = NULL,
      $length   = NULL,
      $context  = NULL;

    protected
      $cat    = NULL;
      
    public function setStream(InputStream $data) {
      $this->data= $data;
    }
    
    public function setContext(SybasexContext $context) {
      $this->context= $context;
    }

    protected function readSmallInt() {
      $short= unpack('vint', $this->data->read(2));
      return $short['int'];
    }
    
    protected function readByte() {
      $byte= unpack('Cbyte', $this->data->read(1));
      return $byte['byte'];
    }
    
    protected function readLong() {
      $long= unpack('Vlong', $this->data->read(4));
      return $long['long'];
    }

    protected function readLength() {
      $this->length= $this->readSmallInt();
      $this->cat && $this->cat->debug($this->getClassName(), '~ length=', $this->length, 'bytes.');
      return $this->length;
    }

    public abstract function handle();
    
    /**
     * Set log facility
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
  }

?>