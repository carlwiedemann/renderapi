<?php

/**
 * @file I am fake Drupal.
 */

include './lib/RenderableBuilder.php';
include './lib/Renderable.php';
include './lib/RenderableDecorator.php';

// Load our fake node module.
include './fake-drupal/modules/node/node.module.php';
include './fake-drupal/modules/node/ThemeFullNode.php';

// Load our fake custom module.
include './fake-drupal/modules/mymodule/mymodule.module.php';
include './fake-drupal/modules/mymodule/ThemeFoo.php';
include './fake-drupal/modules/mymodule/MyModuleFullNodeDecorator.php';

// Load our fake theme.
include './fake-drupal/themes/prague/PragueFullNodeDecorator.php';

/**
 * Dummy registry for callbacks that would alter our builder.
 */
function getAlterCallbacks($builder) {
  $callbacks = array();
  // switch ($builder->getBuildClass()) {
  //   case 'ThemeFullNode':
  //     $callbacks = array(
  //       'mymodule_alter_node_view',
  //     );
  //     break;
  // }
  return $callbacks;
}

/**
 * Dummy registry for modules that may be decorating the renderable.
 */
function getModuleDecoratorClasses($renderable) {
  $classes = array();
  // switch ($renderable->getBuildClass()) {
  //   case 'ThemeFullNode':
  //     $classes = array(
  //       'MyModuleFullNodeDecorator',
  //     );
  //     break;
  // }
  return $classes;
}

/**
 * A registry for decorators that may apply to the renderable.
 */
function getThemeDecoratorClass($renderable) {
  $class = NULL;
  // switch ($renderable->getBuildClass()) {
  //   case 'ThemeFullNode':
  //   case 'MyModuleFullNodeDecorator':
  //     $class = 'PragueFullNodeDecorator';
  //     break;
  // }
  return $class;
}
