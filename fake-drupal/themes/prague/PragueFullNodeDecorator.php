<?php

/**
 * @file Theme decorator class for ThemeFullNode.
 */

class PragueFullNodeDecorator extends RenderableDecorator {
  // Provide template override.
  function getRegisteredTemplate() {
    return './fake-drupal/themes/prague/node.tpl.php';
  }

  // Provide variable overrides.
  function prepare() {
    // Get varaibles from parent templates.
    parent::prepare();
    // Create new variables.
    $this->set('subtitle', 'Here is a subtitle.');
  }
}
