<?php

/**
 * @file Theme class for foo template.
 */

use RenderAPI\AbstractRenderable;

class ThemeFoo extends AbstractRenderable {

  protected $templateName = 'foo';

  function prepare() {
    // Change title variable.
    $this->set('title', $this->get('node')->title . ' overridden by ThemeFoo');
  }

}
