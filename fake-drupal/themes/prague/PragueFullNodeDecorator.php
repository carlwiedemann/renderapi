<?php

/**
 * @file Theme decorator class for ThemeFullNode.
 */

use RenderAPI\AbstractRenderableDecorator;

class PragueFullNodeDecorator extends AbstractRenderableDecorator {
  // Provide template override.
  function getRegisteredTemplate() {
    return './fake-drupal/themes/prague/node.tpl.php';
  }

  // Provide variable overrides.
  function prepare() {
    // Prepare variables from parent objects.
    parent::prepare();
    // Create a new variable.
    $this->set('subtitle', 'Here is a subtitle.');
  }
}
