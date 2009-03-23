<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  $package= 'xp.compiler.syntax.xp';

  uses(
    'text.Tokenizer', 
    'text.StringTokenizer', 
    'text.StreamTokenizer', 
    'io.streams.InputStream',
    'xp.compiler.syntax.xp.Parser', 
    'xp.compiler.emit.Strings', 
    'text.parser.generic.AbstractLexer'
  );

  /**
   * Lexer for XP language
   *
   * @see      xp://text.parser.generic.AbstractLexer
   * @purpose  Lexer
   */
  class xp�compiler�syntax�xp�Lexer extends AbstractLexer {
    protected static
      $keywords  = array(
        'public'        => xp�compiler�syntax�xp�Parser::T_PUBLIC,
        'private'       => xp�compiler�syntax�xp�Parser::T_PRIVATE,
        'protected'     => xp�compiler�syntax�xp�Parser::T_PROTECTED,
        'static'        => xp�compiler�syntax�xp�Parser::T_STATIC,
        'final'         => xp�compiler�syntax�xp�Parser::T_FINAL,
        'abstract'      => xp�compiler�syntax�xp�Parser::T_ABSTRACT,
        'inline'        => xp�compiler�syntax�xp�Parser::T_INLINE,
        'native'        => xp�compiler�syntax�xp�Parser::T_NATIVE,
        
        'package'       => xp�compiler�syntax�xp�Parser::T_PACKAGE,
        'import'        => xp�compiler�syntax�xp�Parser::T_IMPORT,
        'class'         => xp�compiler�syntax�xp�Parser::T_CLASS,
        'interface'     => xp�compiler�syntax�xp�Parser::T_INTERFACE,
        'enum'          => xp�compiler�syntax�xp�Parser::T_ENUM,
        'extends'       => xp�compiler�syntax�xp�Parser::T_EXTENDS,
        'implements'    => xp�compiler�syntax�xp�Parser::T_IMPLEMENTS,
        'instanceof'    => xp�compiler�syntax�xp�Parser::T_INSTANCEOF,

        'operator'      => xp�compiler�syntax�xp�Parser::T_OPERATOR,
        'throws'        => xp�compiler�syntax�xp�Parser::T_THROWS,

        'throw'         => xp�compiler�syntax�xp�Parser::T_THROW,
        'try'           => xp�compiler�syntax�xp�Parser::T_TRY,
        'catch'         => xp�compiler�syntax�xp�Parser::T_CATCH,
        'finally'       => xp�compiler�syntax�xp�Parser::T_FINALLY,
        
        'return'        => xp�compiler�syntax�xp�Parser::T_RETURN,
        'new'           => xp�compiler�syntax�xp�Parser::T_NEW,
        'as'            => xp�compiler�syntax�xp�Parser::T_AS,
        
        'for'           => xp�compiler�syntax�xp�Parser::T_FOR,
        'foreach'       => xp�compiler�syntax�xp�Parser::T_FOREACH,
        'in'            => xp�compiler�syntax�xp�Parser::T_IN,
        'do'            => xp�compiler�syntax�xp�Parser::T_DO,
        'while'         => xp�compiler�syntax�xp�Parser::T_WHILE,
        'break'         => xp�compiler�syntax�xp�Parser::T_BREAK,
        'continue'      => xp�compiler�syntax�xp�Parser::T_CONTINUE,

        'if'            => xp�compiler�syntax�xp�Parser::T_IF,
        'else'          => xp�compiler�syntax�xp�Parser::T_ELSE,
        'switch'        => xp�compiler�syntax�xp�Parser::T_SWITCH,
        'case'          => xp�compiler�syntax�xp�Parser::T_CASE,
        'default'       => xp�compiler�syntax�xp�Parser::T_DEFAULT,
      );

    protected static
      $lookahead= array(
        '.' => array('..' => xp�compiler�syntax�xp�Parser::T_DOTS),
        '-' => array('-=' => xp�compiler�syntax�xp�Parser::T_SUB_EQUAL, '--' => xp�compiler�syntax�xp�Parser::T_DEC),
        '>' => array('>=' => xp�compiler�syntax�xp�Parser::T_GE),
        '<' => array('<=' => xp�compiler�syntax�xp�Parser::T_SE),
        '~' => array('~=' => xp�compiler�syntax�xp�Parser::T_CONCAT_EQUAL),
        '+' => array('+=' => xp�compiler�syntax�xp�Parser::T_ADD_EQUAL, '++' => xp�compiler�syntax�xp�Parser::T_INC),
        '*' => array('*=' => xp�compiler�syntax�xp�Parser::T_MUL_EQUAL),
        '/' => array('/=' => xp�compiler�syntax�xp�Parser::T_DIV_EQUAL),
        '%' => array('%=' => xp�compiler�syntax�xp�Parser::T_MOD_EQUAL),
        '=' => array('==' => xp�compiler�syntax�xp�Parser::T_EQUALS, '=>' => xp�compiler�syntax�xp�Parser::T_DOUBLE_ARROW),
        '!' => array('!=' => xp�compiler�syntax�xp�Parser::T_NOT_EQUALS),
        ':' => array('::' => xp�compiler�syntax�xp�Parser::T_DOUBLE_COLON),
        '|' => array('||' => xp�compiler�syntax�xp�Parser::T_BOOLEAN_OR),
        '&' => array('&&' => xp�compiler�syntax�xp�Parser::T_BOOLEAN_AND),
      );

    const 
      DELIMITERS = " |&?!.:;,@%~=<>(){}[]#+-*/\"'\r\n\t";
    
          
    private
      $ahead   = NULL,
      $comment = NULL;

    /**
     * Constructor
     *
     * @param   var input either a string or an InputStream
     * @param   string source
     */
    public function __construct($input, $source) {
      if ($input instanceof InputStream) {
        $this->tokenizer= new StreamTokenizer($input, self::DELIMITERS, TRUE);
      } else {
        $this->tokenizer= new StringTokenizer($input, self::DELIMITERS, TRUE);
      }
      $this->fileName= $source;
      $this->position= array(1, 1);   // Y, X
    }

    /**
     * Create a new node 
     *
     * @param   xp.compiler.ast.Node
     * @param   bool comment default FALSE whether to pass comment
     * @return  xp.compiler.ast.Node
     */
    public function create($n, $comment= FALSE) {
      $n->position= $this->position;
      if ($comment && $this->comment) {
        $n->comment= $this->comment;
        $this->comment= NULL;
      }
      return $n;
    }
  
    /**
     * Advance this 
     *
     * @return  bool
     */
    public function advance() {
      do {
        $hasMore= $this->tokenizer->hasMoreTokens();
        if ($this->ahead) {
          $token= $this->ahead;
          $this->ahead= NULL;
        } else {
          $token= $this->tokenizer->nextToken(self::DELIMITERS);
        }
        
        // Check for whitespace
        if (FALSE !== strpos(" \n\r\t", $token)) {
          $l= substr_count($token, "\n");
          $this->position[1]= strlen($token) + ($l ? 1 : $this->position[1]);
          $this->position[0]+= $l;
          continue;
        }
        
        $this->position[1]+= strlen($this->value);
        if ("'" === $token{0} || '"' === $token{0}) {
          $this->token= xp�compiler�syntax�xp�Parser::T_STRING;
          $this->value= '';
          do {
            if ($token{0} === ($t= $this->tokenizer->nextToken($token{0}))) {
              // Empty string, e.g. "" or ''
              break;
            }
            $this->value.= $t;
            if ('\\' === $this->value{strlen($this->value)- 1}) {
              $this->value= substr($this->value, 0, -1).$this->tokenizer->nextToken($token{0});
              continue;
            } 
            if ($token{0} !== $this->tokenizer->nextToken($token{0})) {
              throw new IllegalStateException('Unterminated string literal');
            }
            break;
          } while ($hasMore= $this->tokenizer->hasMoreTokens());
          if ('"' === $token{0}) {
            $this->value= Strings::expandEscapesIn($this->value);
          }
        } else if ('$' === $token{0}) {
          $this->token= xp�compiler�syntax�xp�Parser::T_VARIABLE;
          $this->value= $token;
        } else if (isset(self::$keywords[$token])) {
          $this->token= self::$keywords[$token];
          $this->value= $token;
        } else if ('/' === $token{0}) {
          $ahead= $this->tokenizer->nextToken(self::DELIMITERS);
          if ('/' === $ahead) {           // Single-line comment
            $this->tokenizer->nextToken("\n");
            $this->position[1]= 1;
            $this->position[0]++;
            continue;
          } else if ('*' === $ahead) {    // Multi-line comment
            $this->comment= '';
            do { 
              $t= $this->tokenizer->nextToken('/'); 
              $l= substr_count($t, "\n");
              $this->position[1]= strlen($t) + ($l ? 1 : $this->position[1]);
              $this->position[0]+= $l;
              $this->comment.= $t;
            } while ('*' !== $t{strlen($t)- 1});
            $this->tokenizer->nextToken('/');
            continue;
          } else {
            $this->token= ord($token);
            $this->value= $token;
            $this->ahead= $ahead;
          }
        } else if (isset(self::$lookahead[$token])) {
          $ahead= $this->tokenizer->nextToken(self::DELIMITERS);
          $combined= $token.$ahead;
          if (isset(self::$lookahead[$token][$combined])) {
            $this->token= self::$lookahead[$token][$combined];
            $this->value= $combined;
          } else {
            $this->token= ord($token);
            $this->value= $token;
            $this->ahead= $ahead;
          }
        } else if (FALSE !== strpos(self::DELIMITERS, $token) && 1 == strlen($token)) {
          $this->token= ord($token);
          $this->value= $token;
        } else if (ctype_digit($token)) {
          $ahead= $this->tokenizer->nextToken(self::DELIMITERS);
          if ('.' === $ahead{0}) {
            $decimal= $this->tokenizer->nextToken(self::DELIMITERS);
            if (!ctype_digit($decimal)) {
              throw new FormatException('Illegal decimal number "'.$token.$ahead.$decimal.'"');
            }
            $this->token= xp�compiler�syntax�xp�Parser::T_DECIMAL;
            $this->value= $token.$ahead.$decimal;
          } else {
            $this->token= xp�compiler�syntax�xp�Parser::T_NUMBER;
            $this->value= $token;
            $this->ahead= $ahead;
          }
        } else if ('0' === $token{0} && 'x' === @$token{1}) {
          if (!ctype_xdigit(substr($token, 2))) {
            throw new FormatException('Illegal hex number "'.$token.'"');
          }
          $this->token= xp�compiler�syntax�xp�Parser::T_NUMBER;
          $this->value= $token;
        } else {
          $this->token= xp�compiler�syntax�xp�Parser::T_WORD;
          $this->value= $token;
        }
        
        break;
      } while (1);
      
      // fprintf(STDERR, "@ %d,%d: %d `%s`\n", $this->position[0], $this->position[1], $this->token, $this->value);
      return $hasMore;
    }
  }
?>
