<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler';

  uses(
    'io.File',
    'xp.compiler.Compiler',
    'xp.compiler.emit.oel.Emitter',
    'xp.compiler.diagnostic.DefaultDiagnosticListener',
    'xp.compiler.io.FileSource',
    'xp.compiler.io.FileManager',
    'util.log.Logger',
    'util.log.LogAppender'
  );

  /**
   * XP Compiler
   *
   * Usage:
   * <pre>
   * $ xcc [options] [file [file [... ]]]
   * </pre>
   *
   * Options is one of:
   * <ul>
   *   <li>-cp [path]: 
   *     Add path to classpath
   *   </li>
   *   <li>-sp [path]: 
   *     Adds path to source path (source path will equal classpath initially)
   *   </li>
   *   <li>-e [emitter]: 
   *     Use emitter, one of "oel" or "source", defaults to "source"
   *   </li>
   *   <li>-o [outputdir]: 
   *     Writed compiled files to outputdir
   *   </li>
   *   <li>-t [level[,level[...]]]:
   *     Set trace level (all, none, info, warn, error, debug)
   *   </li>
   * </ul>
   *
   * @purpose  Runner
   */
  class xp�compiler�Runner extends Object {
  
    /**
     * Converts api-doc "markup" to plain text w/ ASCII "art"
     *
     * @param   string markup
     * @return  string text
     */
    protected static function textOf($markup) {
      $line= str_repeat('=', 72);
      return strip_tags(preg_replace(array(
        '#<pre>#', '#</pre>#', '#<li>#',
      ), array(
        $line, $line, '* ',
      ), trim($markup)));
    }

    /**
     * Shows usage and exits
     *
     */
    protected function showUsage() {
      Console::$err->writeLine(self::textOf(XPClass::forName(xp::nameOf(__CLASS__))->getComment()));
      exit(2);
    }
    
    /**
     * Entry point method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      if (empty($args)) self::showUsage();
      
      $compiler= new Compiler();
      $manager= new FileManager();
      $manager->setSourcePaths(xp::$registry['classpath']);
      $emitter= 'source';
      
      // Handle arguments
      $files= array();
      $listener= new DefaultDiagnosticListener(Console::$out);
      for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
        if ('-?' === $args[$i] || '--help' === $args[$i]) {
          self::showUsage();
        } else if ('-cp' === $args[$i]) {
          ClassLoader::registerPath($args[++$i]);
        } else if ('-sp' === $args[$i]) {
          $manager->addSourcePath($args[++$i]);
        } else if ('-t' === $args[$i]) {
          $levels= LogLevel::NONE;
          foreach (explode(',', $args[++$i]) as $level) {
            $levels |= LogLevel::named($level);
          }
          $appender= newinstance('util.log.Appender', array(), '{
            public function append(LoggingEvent $event) {
              Console::$err->write($this->layout->format($event));
            }
          }');
          $compiler->setTrace(Logger::getInstance()->getCategory()->withAppender($appender, $levels));
        } else if ('-e' === $args[$i]) {
          $emitter= $args[++$i];
        } else if ('-o' === $args[$i]) {
          $manager->setOutput(new Folder($args[++$i]));
        } else {
          $files[]= new FileSource(new File($args[$i]));
        }
      }
      
      // Check
      if (empty($files)) {
        Console::$err->writeLine('*** No files given (-? will show usage)');
        exit(2);
      }
      
      // Compile files
      $success= $compiler->compile(
        $files, 
        $listener, 
        $manager, 
        Package::forName('xp.compiler.emit')->getPackage($emitter)->loadClass('Emitter')->newInstance()
      );
      exit($success ? 0 : 1);
    }
  }
?>
