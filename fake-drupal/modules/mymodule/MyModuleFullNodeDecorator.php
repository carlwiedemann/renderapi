<?php

/**
 * @file Something that we'd see in a custom module
 */

/**
 * Implemented as a decorator via theme registry.
 */
class MyModuleFullNodeDecorator extends RenderableDecorator {
  function getRegisteredTemplate() {
    return parent::getRegisteredTemplate();
  }

  function prepare() {
    // Prepare default variables.
    parent::prepare();
    $this->set('title', $this->get('node')->title . ' modified by MyModuleFullNodeDecorator');
  }
}
