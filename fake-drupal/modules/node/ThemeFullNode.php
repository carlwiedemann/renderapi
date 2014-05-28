<?php

/**
 * @file Theme class for node.tpl.php
 */

use RenderAPI\AbstractRenderable;

class ThemeFullNode extends AbstractRenderable {

  protected $templateName = 'node';

  // Provide variable overrides.
  function prepare() {
    // Set a title variable.
    $this->set('title', $this->get('node')->title);
  }
}
