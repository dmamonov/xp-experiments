<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Negates another criterion
   *
   * @purpose  Criterion
   */
  class NegationExpression extends Object implements Criterion {
    public
      $criterion  = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &rdbms.criterion.Criterion criterion
     */
    public function __construct(&$criterion) {
      $this->criterion= &$criterion;
    }
  
    /**
     * Returns the fragment SQL
     *
     * @access  public
     * @param   &rdbms.DBConnection conn
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(&$conn, $types) { 
      return $conn->prepare('not (%c)', $this->criterion->asSql($conn, $types));
    }

  } 
?>
