<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'AllClassesIterator',
    'io.Folder',
    'io.File',
    'io.FileUtil',
    'xml.Tree',
    'text.doclet.Doclet',
    'text.doclet.markup.MarkupBuilder',
    'util.collections.HashSet',
    'io.collections.CollectionComposite', 
    'io.collections.FileCollection', 
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.ExtensionEqualsFilter'
  );
  
  // {{{ GeneratorDoclet
  //     Specialized doclet
  class GeneratorDoclet extends Doclet {
    protected
      $build    = NULL,
      $markup   = NULL,
      $packages = array();

    function tagAttribute($tags, $which, $attribute= 'text') {
      return isset($tags[$which]) ? $tags[$which]->{$attribute} : NULL;
    }
    
    function annotationNode($list) {
      $n= new Node('annotations');
      foreach ($list as $annotation) {
        $a= $n->addChild(new Node('annotation', NULL, array('name' => $annotation->name())));
        if (is_array($annotation->value)) {
          $a->addChild(Node::fromArray($annotation->value, 'value'));
        } else if (is_scalar($annotation->value)) {
          $a->addChild(new Node('value', $annotation->value, array('type' => gettype($annotation->value))));
        } else if (NULL === $annotation->value) {
          $a->addChild(new Node('value', NULL, array('type' => '')));
        } else {
          $a->addChild(new Node('value', xp::stringOf($annotation->value), array('type' => xp::typeOf($annotation->value))));
        }
      }
      return $n;
    }
    
    function classReferenceNode($classdoc) {
      $n= new Node('link', NULL, array(
        'rel'     => 'class',
        'href'    => $classdoc->qualifiedName(),
      ));
      $this->marshalClassDoc($classdoc);
      return $n;
    }
    
    function methodsNode($classdoc, $inherited= FALSE) {
      $n= new Node('methods');
      $inherited && $n->setAttribute('from', $classdoc->qualifiedName());

      foreach ($classdoc->methods as $method) {
        $m= $n->addChild(new Node('method', NULL, array(
          'name'   => $method->name(),
          'access' => $method->getAccess(),
          'return' => $this->tagAttribute($method->tags('return'), 0, 'type')
        )));
        
        $m->addChild(Node::fromArray($method->getModifiers(), 'modifiers'));

        // Apidoc
        $m->addChild(new Node('comment', $this->markup($method->commentText())));
        foreach ($method->tags('see') as $ref) {
          $m->addChild(new Node('see', $ref->text, array(
            'scheme' => $ref->scheme,
            'href'   => $ref->urn
          )));
        }

        // Annotations
        $m->addChild($this->annotationNode($method->annotations()));
        
        // Thrown exceptions
        foreach ($method->tags('throws') as $thrown) {
          $m->addChild(new Node('exception', $thrown->text, array(
            'class' => $thrown->exception->qualifiedName()
          )));
        }
        
        // Arguments
        $param= array();
        foreach ($method->tags('param') as $tag) {
          $param['$'.$tag->name]= $tag;
        }
        foreach ($method->arguments as $name => $default) {
          $a= $m->addChild(new Node('argument', NULL, array('name' => $name)));
          $a->addChild(new Node('default', $default));
          if (isset($param[$name])) {
            $a->setAttribute('type', $param[$name]->type);
            $a->addChild(new Node('comment', $param[$name]->text));
          } else {
            $a->setAttribute('type', 'mixed');
            // DEBUG Console::writeLine('Unknown ', $name, ' in  ', xp::stringOf($method->tags('param')));
          }
        }
      }
      return $n;
    }

    function fieldsNode($classdoc, $inherited= FALSE) {
      $n= new Node('fields');
      $inherited && $n->setAttribute('from', $classdoc->qualifiedName());

      foreach ($classdoc->fields as $field) {
        $f= $n->addChild(new Node('field', NULL, array(
          'name'   => $field->name(),
          'access' => $field->getAccess(),
        )));
        $f->addChild(new Node('constant', $field->constantValue()));
        $f->addChild(Node::fromArray($field->getModifiers(), 'modifiers'));
      }
      
      return $n;
    }

    function classNode($classdoc) {
      $n= new Node('class', NULL, array(
        'name'    => $classdoc->qualifiedName(),
        'package' => $classdoc->containingPackage()->name(),
        'type'    => $classdoc->classType()
      ));

      $n->addChild(Node::fromArray($classdoc->getModifiers(), 'modifiers'));
      
      // Apidoc
      $n->addChild(new Node('comment', $this->markup($classdoc->commentText())));
      $n->addChild(new Node('purpose', $this->tagAttribute($classdoc->tags('purpose'), 0, 'text')));
      foreach ($classdoc->tags('see') as $ref) {
        $n->addChild(new Node('see', $ref->text, array(
          'scheme' => $ref->scheme,
          'href'   => $ref->urn
        )));
      }
      foreach ($classdoc->tags('test') as $ref) {
        $n->addChild(new Node('test', NULL, array('href' => $ref->text)));
      }
      if ($classdoc->tags('deprecated')) {
        $n->addChild(new Node('deprecated', $this->tagAttribute($classdoc->tags('deprecated'), 0, 'text')));
      }

      // Annotations
      $n->addChild($this->annotationNode($classdoc->annotations()));
      
      // Constants
      foreach ($classdoc->constants as $name => $value) {
        $n->addChild(new Node('constant', $value, array('name' => $name)));
      }

      // Superclasses
      $extends= $n->addChild(new Node('extends'));
      $doc= $classdoc;
      while ($doc= $doc->superclass) {
        $extends->addChild($this->classReferenceNode($doc));
      }
      
      // Interfaces
      $interfaces= $n->addChild(new Node('implements'));
      for ($classdoc->interfaces->rewind(); $classdoc->interfaces->hasNext(); ) {
        $interfaces->addChild($this->classReferenceNode($classdoc->interfaces->next()));
      }

      // Members
      $doc= $classdoc;
      $inherited= FALSE;
      do {
        $n->addChild($this->fieldsNode($doc, $inherited));
        $n->addChild($this->methodsNode($doc, $inherited));
        $inherited= TRUE;
      } while ($doc= $doc->superclass);
      
      return $n;
    }
    
    function marshalClassDoc($classdoc) {
      static $done= array();
      
      if (isset($done[$classdoc->hashCode()])) return;    // Already been there

      $out= new File($this->build->getURI().$classdoc->qualifiedName().'.xml');
      Console::writeLine('- ', $classdoc->toString());

      // Add contained package
      $package= $classdoc->containingPackage();
      $hash= $package->hashCode();

      if (!isset($this->packages[$hash])) {
        $this->packages[$hash]= array('info' => $package);
      }

      $this->packages[$hash]['classes'][$classdoc->name()]= $classdoc->classType();

      // Create XML tree
      $tree= new Tree('doc');
      $tree->addChild($this->classNode($classdoc));

      // Write to file
      FileUtil::setContents(
        $out,
        $tree->getDeclaration()."\n".$tree->getSource(INDENT_DEFAULT)
      );
      
      $done[$classdoc->hashCode()]= TRUE;
      delete($out);
      delete($tree);
    }
    
    function markup($comment) {
      return new PCData('<p>'.$this->markup->markupFor($comment).'</p>');
    }

    function start($root) {
      $this->build= new Folder($root->option('build', 'build'));
      $this->build->exists() || $this->build->create();
      
      $this->markup= new MarkupBuilder();
      
      // Marshal classes
      $this->packages= array();
      while ($root->classes->hasNext()) {
        $this->marshalClassDoc($root->classes->next());
        xp::gc();
      }
      
      // Marshal packages
      foreach ($this->packages as $package) {
        Console::writeLine('- ', $package['info']->toString());

        $tree= new Tree('doc');
        $p= $tree->addChild(new Node('package', NULL, array('name' => $package['info']->name())));
        $p->addChild(new Node('comment', $this->markup($package['info']->commentText())));

        $p->addChild(new Node('purpose', $this->tagAttribute($package['info']->tags('purpose'), 0, 'text')));
        foreach ($package['info']->tags('see') as $ref) {
          $p->addChild(new Node('see', $ref->text, array(
            'scheme' => $ref->scheme,
            'href'   => $ref->urn
          )));
        }
        foreach ($package['info']->tags('test') as $ref) {
          $p->addChild(new Node('test', NULL, array('href' => $ref->text)));
        }
        if ($package['info']->tags('deprecated')) {
          $p->addChild(new Node('deprecated', $this->tagAttribute($package['info']->tags('deprecated'), 0, 'text')));
        }

        foreach ($package['classes'] as $name => $type) {
          $p->addChild(new Node('class', NULL, array(
            'name' => $name,
            'type' => $type
          )));
        }

        // Write to file
        $out= new File($this->build->getURI().$package['info']->name().'.xml');
        FileUtil::setContents(
          $out,
          $tree->getDeclaration()."\n".$tree->getSource(INDENT_DEFAULT)
        );

        delete($out);
        delete($tree);
      }
    }

    function iteratorFor($root, $classes) {
      $collections= array();
      foreach (explode(PATH_SEPARATOR, $root->option('scan')) as $path) {
        $scan= new Folder($path);
        if (!$scan->exists()) {
          throw new IllegalArgumentException($scan->getURI().' does not exist!');
        }
     
        $collections[]= new FileCollection($scan->getURI());
      }

      $iterator= new AllClassesIterator(
        new FilteredIOCollectionIterator(
          new CollectionComposite($collections), 
          new ExtensionEqualsFilter('class.php'),
          TRUE
        ),
        ini_get('include_path')
      );
      $iterator->root= $root;
      return $iterator;
    }
    
    
    function validOptions() {
      return array(
        'scan'  => HAS_VALUE,
        'build' => HAS_VALUE
      );
    }
  }
  // }}}
  
  // {{{ main
  RootDoc::start(new GeneratorDoclet(), new ParamString());
  // }}}
?>
