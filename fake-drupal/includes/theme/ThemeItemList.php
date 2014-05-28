<?php

/**
 * @file Theme callback for an item list.
 */

use RenderAPI\AbstractRenderable;

class ThemeItemList extends AbstractRenderable {

  protected $templateName = 'item-list';

  function prepare() {
    // Default to unordered list.
    if (!$this->exists('type')) {
      $this->set('type', 'ul');
    }
  }
}
