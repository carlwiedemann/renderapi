<?php

/**
 * @file Theme class for foo.tpl.php.
 */

use RenderAPI\AbstractRenderable;

class ThemeFoo extends AbstractRenderable {
  // Provide template via some dummy registry.
  function getRegisteredTemplate() {
    return './fake-drupal/modules/mymodule/foo.tpl.php';
  }

  // Prepare variables for the foo.tpl.php template.
  function prepare() {
    // Change title variable.
    $this->set('title', $this->get('node')->title . ' overridden by ThemeFoo');
  }
}
