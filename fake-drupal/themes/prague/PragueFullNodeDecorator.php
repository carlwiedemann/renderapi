<?php

/**
 * @file Something that we'd see in prague.theme.
 */

/**
 * Implemented as a decorator via theme registry.
 */
class PragueFullNodeDecorator extends RenderableDecorator {
  static function getRegisteredTemplate() {
    return './fake-drupal/themes/prague/node.tpl.php';
  }
  function prepare() {
    parent::prepare();
    $this->set('subtitle', $this->get('title') . ' is the title, for real.');
  }
}
