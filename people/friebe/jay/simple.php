import xp~lang~IllegalArgumentException, xp~util~DateFormatException;

package de~thekid { 
  class Date extends xp~lang~Object {
    private $stamp= 0;
    protected static $tz= array(
      'GMT' => 0
    ); 

    public __construct($stamp) throws IllegalArgumentException, DateFormatException {
      $this->setStamp($stamp);
    }

    [@override] public string toString() {
      return date('r', $this->stamp);
    }

    public static operator + (Date $a, Date $b) {
      return new Date($a->stamp + $b->stamp);
    }

    public Comparator comparator() {
      return new Comparator() {
        public bool compare(Date $a, Date $b) {
          return strcmp($a->stamp, $b->stamp);
        }
      };
    }

    private void setStamp($stamp) {
      $this->stamp= $stamp;
    }
  }

  enum Coin {
    penny(1), nickel(5), dime(10), quarter(25);
  }

  interface Comparator {
    bool compare($a, $b);
  }
}

try {
  $one= new Date(1);
} catch (IllegalArgumentException $e) {
  echo $e;
  exit;
} catch (DateFormatException $e) {
  echo $e;
  exit;
} catch (xp~lang~Exception $e) {
  echo $e;
  exit;
}

echo $one, "\n";

$coin= Coin::penny();
switch ($coin) {
  case Coin::penny: if ($debug) echo "It's a penny!\n"; break;
  default: echo "Unknown coin...\n";
}
