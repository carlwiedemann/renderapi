<?php

/**
 * @file I am fake Drupal.
 */

include './lib/RenderableBuilder.php';
include './lib/Renderable.php';
include './lib/RenderableDecorator.php';

include './fake-drupal/modules/node/ThemeFullNode.php';

include './fake-drupal/themes/prague/PragueFullNodeDecorator.php';

include './fake-drupal/modules/mymodule/mymodule.module.php';
include './fake-drupal/modules/mymodule/ThemeFoo.php';
include './fake-drupal/modules/mymodule/MyModuleFullNodeDecorator.php';

function node_load($nid) {
  return (object) array(
    'nid' => $nid,
    'title' => 'I am a node',
  );
}

/**
 * Dummy registry for callbacks that would alter our builder.
 */
function getAlterCallbacks() {
  return array(
    'mymodule_alter_node_view',
  );
}

/**
 * Dummy registry for modules that may be decorating the renderable.
 */
function getModuleDecoratorClasses($renderable) {
  $classes = array();
  switch ($renderable->getBuildClass()) {
    case 'ThemeFullNode':
      $classes = array(
        'MyModuleFullNodeDecorator',
      );
      break;
  }
  return $classes;
}

/**
 * A registry for decorators that may apply to the renderable.
 */
function getThemeDecoratorClass($renderable) {
  $class = NULL;
  switch ($renderable->getBuildClass()) {
    case 'ThemeFullNode':
    case 'MyModuleFullNodeDecorator':
      $class = 'PragueFullNodeDecorator';
      break;
  }
  return $class;
}
