<?php

/**
 * @file Theme decorator class for ThemeFullNode.
 */

class MyModuleFullNodeDecorator extends AbstractRenderableDecorator {
  // Simply use parent template definition.
  function getRegisteredTemplate() {
    return parent::getRegisteredTemplate();
  }

  // Provide variable overrides.
  function prepare() {
    // Prepare variables from parent objects.
    parent::prepare();
    // Change title variable.
    $this->set('title', $this->get('node')->title . ' modified by MyModuleFullNodeDecorator');
  }
}
