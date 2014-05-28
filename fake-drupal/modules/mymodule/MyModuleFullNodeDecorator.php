<?php

/**
 * @file Theme decorator class for ThemeFullNode.
 */

use RenderAPI\AbstractRenderableDecorator;

class MyModuleFullNodeDecorator extends AbstractRenderableDecorator {
  // Provide variable overrides.
  function prepare() {
    // Prepare variables from parent objects.
    parent::prepare();
    // Change title variable.
    $this->set('title', $this->get('node')->title . ' modified by MyModuleFullNodeDecorator');
  }
}
