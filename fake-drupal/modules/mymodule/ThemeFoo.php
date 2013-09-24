<?php


class ThemeFoo extends Renderable {
  static function getRegisteredTemplate() {
    return './fake-drupal/modules/mymodule/foo.tpl.php';
  }

  function prepare() {
    parent::parepare();
  }
}
