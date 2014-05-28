<?php

/**
 * @file An example theme callback
 */

use RenderAPI\AbstractRenderable;

class ThemeSomeExamples extends AbstractRenderable {
  function getRegisteredTemplate() {
    return './fake-drupal/includes/theme/templates/some-examples.tpl.php';
  }

  function prepare() {
  }
}
