<?php

/**
 * @file Handler for page.tpl.php
 */

use RenderAPI\AbstractRenderable;

class ThemePage extends AbstractRenderable {
  // Provide template via some dummy registry.
  function getRegisteredTemplate() {
    return './fake-drupal/includes/theme/templates/page.tpl.php';
  }

  function prepare() {
  }
}
