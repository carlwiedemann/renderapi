<?php

/**
 * @file Theme class for node.tpl.php
 */

class ThemeFullNode extends Renderable {
  // Provide default template.
  function getRegisteredTemplate() {
    return './fake-drupal/modules/node/node.tpl.php';
  }

  function prepare() {
    // Prep variables for the template.
    $this->set('title', $this->get('node')->title);
  }
}
