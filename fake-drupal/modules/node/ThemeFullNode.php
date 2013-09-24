<?php


// An actual renderable. The equivalent of a theme callback.
class ThemeFullNode extends Renderable {
  static function getRegisteredTemplate() {
    return './fake-drupal/modules/node/node.tpl.php';
  }

  function prepare() {
    // Prep variables for the template.
    // $this->set('title', $this->get('node')->title);
  }
}
