<?php

/**
 * @file Something that we'd see in a custom module
 */

/**
 * Implemented as a decorator via theme registry.
 */
class MyModuleFullNodeDecorator extends RenderableDecorator {
  static function getRegisteredTemplate() {
    return parent::getRegisteredTemplate();
  }
  
  function prepare() {
    parent::prepare();
    $this->set('title', $this->get('title') . ' is the title.');
  }
}
