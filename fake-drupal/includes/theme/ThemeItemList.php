<?php

/**
 * @file Theme callback for an item list.
 */

class ThemeItemList extends Renderable {
  // Provide template via some dummy registry.
  function getRegisteredTemplate() {
    return './fake-drupal/includes/theme/templates/item-list.tpl.php';
  }

  function prepare() {
    // Default to unordered list.
    if (!$this->exists('type')) {
      $this->set('type', 'ul');
    }
  }
}
