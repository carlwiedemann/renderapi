<?php

/**
 * @file I am fake Drupal!
 */

use Silex\Application;

// Load our fake node module.
include './fake-drupal/modules/node/node.module.php';
include './fake-drupal/modules/node/ThemeFullNode.php';

// Load our fake custom module.
include './fake-drupal/modules/mymodule/mymodule.module.php';
include './fake-drupal/modules/mymodule/ThemeFoo.php';
include './fake-drupal/modules/mymodule/MyModuleFullNodeDecorator.php';

// Load fake common components.
include './fake-drupal/includes/theme/ThemeSomeExamples.php';
include './fake-drupal/includes/theme/ThemeItemList.php';
include './fake-drupal/includes/theme/ThemePage.php';

// Load our fake theme.
include './fake-drupal/themes/prague/PragueFullNodeDecorator.php';

/**
 * Fakes a registry for callbacks that would alter our builder. @todo Revise.
 */
function getAlterCallbacks($builder) {
  $callbacks = array();
  // switch ($builder->getBuildClass()) {
  //   case 'ThemeFullNode':
  //     $callbacks = array(
  //       'mymodule_alter_node_view',
  //     );
  //     break;
  //   case 'ThemeItemList':
  //     $callbacks = array(
  //       'mymodule_alter_item_list',
  //     );
  //     break;
  // }
  return $callbacks;
}

/**
 * Fakes a registry for modules that may be decorating the renderable. @todo Revise.
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
 * Fakes registry for decorators that may apply to the renderable. @todo Revise.
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

/**
 * Fakes module and theme registry. @todo Revise.
 */
function getTemplateDirectories() {
  return array(
    'node' => (object) array(
      'type' => 'module',
      'name' => 'node',
      'dir' => __DIR__ . '/modules/node',
    ),
    // 'mymodule' => (object) array(
    //   'type' => 'module',
    //   'name' => 'mymodule',
    //   'dir' => __DIR__ . '/modules/mymodule',
    // ),
    // 'prague' => (object) array(
    //   'type' => 'theme',
    //   'name' => 'prague',
    //   'dir' => __DIR__ . '/themes/prague',
    // ),
  );
}

/**
 * Fakes theme directory registry.
 */
function getTwigThemeDirectories() {
  // 'core' theme directory.
  $directories = array(
    './fake-drupal/includes/theme',
  );
  foreach (array_reverse(getTemplateDirectories()) as $name => $moduleData) {
    $directories[] = $moduleData->dir;
  }
  return $directories;
}
