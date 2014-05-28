<?php

/**
 * @file Theme class for node.tpl.php
 */

use RenderAPI\AbstractRenderable;

class ThemeFullNode extends AbstractRenderable {
  // Provide template via some dummy registry.
  function getRegisteredTemplate() {
    return './fake-drupal/modules/node/node.tpl.php';
  }

  // Provide variable overrides.
  function prepare() {
    // Set a title variable.
    $this->set('title', $this->get('node')->title);
  }
}
