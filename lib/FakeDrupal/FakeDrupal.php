<?php

/**
 * @file I am fake Drupal and I need some love yes?
 */

namespace FakeDrupal;

// Load our fake node module. @todo Revise.
include './fake-drupal/modules/node/node.module.php';
include './fake-drupal/modules/node/ThemeFullNode.php';

// Load our fake custom module. @todo Revise.
include './fake-drupal/modules/mymodule/mymodule.module.php';
include './fake-drupal/modules/mymodule/ThemeFoo.php';
include './fake-drupal/modules/mymodule/MyModuleFullNodeDecorator.php';

// Load fake common components. @todo Revise.
include './fake-drupal/includes/theme/ThemeSomeExamples.php';
include './fake-drupal/includes/theme/ThemeItemList.php';
include './fake-drupal/includes/theme/ThemePage.php';

// Load our fake theme. @todo Revise.
include './fake-drupal/themes/prague/PragueFullNodeDecorator.php';


class FakeDrupal {

  /**
   * Fakes a registry for callbacks that would alter our builder. @todo Revise.
   */
  public function getAlterCallbacks($builder) {
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
  public function getModuleDecoratorClasses($renderable) {
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
  public function getThemeDecoratorClass($renderable) {
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
  public function getTemplateDirectories() {
    return array(
      'node' => (object) array(
        'type' => 'module',
        'name' => 'node',
        'dir' => './fake-drupal/modules/node',
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
  public function getTwigThemeDirectories() {
    // 'core' theme directory.
    $directories = array(
      './fake-drupal/includes/theme',
    );
    foreach (array_reverse(FakeDrupal::getTemplateDirectories()) as $name => $moduleData) {
      $directories[] = $moduleData->dir;
    }
    return $directories;
  }
}
