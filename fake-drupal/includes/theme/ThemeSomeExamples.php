<?php

/**
 * @file An example theme callback
 */
class ThemeSomeExamples extends AbstractRenderable {
  function getRegisteredTemplate() {
    return './fake-drupal/includes/theme/templates/some-examples.tpl.php';
  }

  function prepare() {
  }
}
