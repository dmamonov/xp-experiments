<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  $package= 'xp.ide.wrapper';

  uses(
    'xp.ide.wrapper.Wrapper'
  );

  /**
   * Nedit ide Wrapper
   *
   * @purpose IDE
   */
  class xp�ide�wrapper�Nedit extends xp�ide�wrapper�Wrapper {

    /**
     * complete the source under the cursor
     *
     * @param  xp.ide.Cursor cursor
     */
    #[@action(name='complete', args="Cursor")]
    public function complete(xp�ide�Cursor $cursor) {
      $response= $this->ide->complete($cursor);
      $this->out->write(
        $response->getSnippet()->getPosition().PHP_EOL
        .strlen($response->getSnippet()->getText()).PHP_EOL
        .count($response->getSuggestions()).PHP_EOL
        .implode(PHP_EOL, $response->getSuggestions())
      );
    }

    /**
     * grep the file URI where the XP class
     * under the cursor if defined
     *
     * @param  xp.ide.Cursor cursor
     */
    #[@action(name='grepclassfile', args="Cursor")]
    public function grepClassFileUri(xp�ide�Cursor $cursor) {
      $response= $this->ide->grepClassFileUri($cursor);
      list($scheme, $rest)= explode('://', $response->getUri(), 2);
      if ('file' !== $scheme) throw new IllegalArgumentException(sprintf('Cannot open class "%s" from location %s', $response->getSnippet()->getText(), $response->getUri()));
      $this->out->write($rest);
    }

    /**
     * check syntax
     *
     * @param  xp.ide.lint.ILanguage language
     */
    #[@action(name='checksyntax', args="Language")]
    public function checkSyntax(xp�ide�lint�ILanguage $language) {
      $errors= $this->ide->checkSyntax($language);
      if (0 == sizeOf($errors)) {
        $this->out->write("0".PHP_EOL."0".PHP_EOL.PHP_EOL);
        return;
      }
      foreach ($errors as $e) {
        $this->out->write(
          $e->getLine().PHP_EOL
          .$e->getColumn().PHP_EOL
          .$e->getText()
        );
      }
    }
  }
?>
