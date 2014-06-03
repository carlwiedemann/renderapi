<?php

/**
 * @file Theme class for node template.
 */

use RenderAPI\AbstractRenderable;

class ThemeNode extends AbstractRenderable {

  protected $templateName = 'node';

  function prepare() {

    // Setup variables available in template.
    $this->set('title', $this->get('node')->title);

    $this->set('content', $this->get('node')->body);

  }

}
