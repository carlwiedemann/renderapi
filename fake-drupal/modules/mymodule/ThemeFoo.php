<?php

/**
 * @file Theme class for foo.tpl.php.
 */

class ThemeFoo extends Renderable {
  // Provide template via some dummy registry.
  function getRegisteredTemplate() {
    return './fake-drupal/modules/mymodule/foo.tpl.php';
  }

  // Prepare variables for the foo.tpl.php template.
  function prepare() {
    $this->set('title', $this->get('node')->title . ' overridden by ThemeFoo');
  }
}
