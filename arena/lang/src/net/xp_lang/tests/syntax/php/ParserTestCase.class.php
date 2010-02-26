<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'tests.syntax.php';

  uses(
    'unittest.TestCase',
    'xp.compiler.syntax.php.Lexer',
    'xp.compiler.syntax.php.Parser',
    'xp.compiler.ast.Node'
  );

  /**
   * Base class for all other parser test cases.
   *
   */
  abstract class tests�syntax�php�ParserTestCase extends TestCase {
  
    /**
     * Parse method source and return statements inside this method.
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      try {
        return create(new xp�compiler�syntax�php�Parser())->parse(new xp�compiler�syntax�php�Lexer('<?php class Container {
          public function method() {
            '.$src.'
          }
        } ?>', '<string:'.$this->name.'>'))->declaration->body['methods'][0]->body;
      } catch (ParseException $e) {
        throw $e->getCause();
      }
    }

    /**
     * Create a node at a given position
     *
     * @param   xp.compiler.ast.Node n
     * @param   int[2] pos
     * @return  xp.compiler.ast.Node
     */
    protected function create(xp�compiler�ast�Node $n, $pos) {
      $n->position= $pos;
      return $n;
    }
  }
?>
