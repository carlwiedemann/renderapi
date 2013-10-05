<?php

/**
 * @file Handler for page.tpl.php
 */
class ThemePage extends Renderable {
  // Provide template via some dummy registry.
  function getRegisteredTemplate() {
    return './fake-drupal/includes/theme/templates/page.tpl.php';
  }

  function prepare() {
  }
}
