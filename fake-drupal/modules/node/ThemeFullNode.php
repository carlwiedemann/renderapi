<?php

/**
 * @file Theme class for node.tpl.php
 */

class ThemeFullNode extends Renderable {
  // Provide template via some dummy registry.
  function getRegisteredTemplate() {
    return './fake-drupal/modules/node/node.tpl.php';
  }

  // Provide variable overrides.
  function prepare() {
    // Prepare variables from parent objects.
    parent::prepare();
    // Set a title variable.
    $this->set('title', $this->get('node')->title);
  }
}
