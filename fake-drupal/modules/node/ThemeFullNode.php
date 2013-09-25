<?php

/**
 * @file Theme class for node.tpl.php
 */

class ThemeFullNode extends Renderable {
  // Provide template via some dummy registry.
  function getRegisteredTemplate() {
    return './fake-drupal/modules/node/node.tpl.php';
  }

  function prepare() {
    // Get varaibles from parent templates.
    parent::prepare();
    // Prep variables for the template.
    $this->set('title', $this->get('node')->title);
  }
}
