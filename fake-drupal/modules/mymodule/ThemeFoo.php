<?php

/**
 * @file Theme class for foo.tpl.php.
 */

use RenderAPI\AbstractRenderable;

class ThemeFoo extends AbstractRenderable {
  protected $templateName = 'foo';

  // Prepare variables for the foo.tpl.php template.
  function prepare() {
    // Change title variable.
    $this->set('title', $this->get('node')->title . ' overridden by ThemeFoo');
  }
}
