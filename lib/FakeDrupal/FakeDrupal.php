<?php

/**
 * @file I am fake Drupal and I need some love yes?
 */

namespace FakeDrupal;

use RenderAPI\RenderableBuilderInterface;
use RenderAPI\RenderableInterface;

// Load our fake node module. @todo Revise.
include './fake-drupal/modules/node/node.module.php';
include './fake-drupal/modules/node/ThemeFullNode.php';

// Load our fake custom module. @todo Revise.
include './fake-drupal/modules/mymodule/mymodule.module.php';
include './fake-drupal/modules/mymodule/ThemeFoo.php';
include './fake-drupal/modules/mymodule/MyModuleFullNodeDecorator.php';

// Load fake common components. @todo Revise.
include './fake-drupal/includes/theme/ThemeItemList.php';
include './fake-drupal/includes/theme/ThemePage.php';
include './fake-drupal/includes/theme/ThemeSomeExamples.php';

// Load our fake theme. @todo Revise.
include './fake-drupal/themes/prague/PragueFullNodeDecorator.php';

class FakeDrupal {

  /**
   * This fakes out a registry that would otherwise be populated via some config
   * or auto-discovery.
   */
  public static function getRegistry() {
    $baseTemplateDirectories = array(
      './fake-drupal/themes/prague',
    );
    return (object) array(
      'ThemeFullNode' => (object) array(
        'sourceName' => 'node',
        'sourceType' => 'module',
        'alterCallbacks' => array(
          'mymodule_alter_node_view',
        ),
        'decoratorClasses' => array(
          'MyModuleFullNodeDecorator',
          'PragueFullNodeDecorator',
        ),
        'templateDirectories' => array(
          './fake-drupal/modules/node',
        ) + $baseTemplateDirectories,
      ),
      'ThemeItemList' => (object) array(
        'sourceName' => 'core',
        'sourceType' => 'module',
        'alterCallbacks' => array(
          'mymodule_alter_item_list',
        ),
        'decoratorClasses' => array(),
        'templateDirectories' => array(
          './fake-drupal/includes/theme',
          ) + $baseTemplateDirectories,
      ),
      'ThemeSomeExamples' => (object) array(
        'sourceName' => 'core',
        'sourceType' => 'module',
        'alterCallbacks' => array(),
        'decoratorClasses' => array(),
        'templateDirectories' => array(
          './fake-drupal/includes/theme',
        ) + $baseTemplateDirectories,
      ),
      'ThemePage' => (object) array(
        'sourceName' => 'core',
        'sourceType' => 'module',
        'alterCallbacks' => array(),
        'decoratorClasses' => array(),
        'templateDirectories' => array(
          './fake-drupal/includes/theme',
        ) + $baseTemplateDirectories,
      ),
      'ThemeFoo' => (object) array(
        'sourceName' => 'mymodule',
        'sourceType' => 'module',
        'alterCallbacks' => array(),
        'decoratorClasses' => array(),
        'templateDirectories' => array(
          './fake-drupal/modules/mymodule',
        ) + $baseTemplateDirectories,
      ),
    );
  }

  public static function getRegistryEntry($className) {
    $registry = FakeDrupal::getRegistry();
    return isset($registry->$className) ? $registry->$className : NULL;
  }

  public static function getAlterCallbacks(RenderableBuilderInterface $builder) {
    $entry = FakeDrupal::getRegistryEntry($builder->getBuildClass());
    return isset($entry) ? $entry->alterCallbacks : array();
  }

  public static function getDecoratorClasses(RenderableInterface $renderable) {
    $entry = FakeDrupal::getRegistryEntry($renderable->getBuildClass());
    return isset($entry) ? $entry->decoratorClasses : array();
  }

  public static function getTemplateDirectories(RenderableInterface $renderable) {
    $entry = FakeDrupal::getRegistryEntry($renderable->getBuildClass());
    return isset($entry) ? $entry->templateDirectories : array();
  }

  /**
   * Returns a fake ranking of directories in which to look for templates.
   */
  public static function getWeightedTemplateDirectories() {
    return array(
      './fake-drupal/includes/theme',
      './fake-drupal/modules/node',
      './fake-drupal/modules/mymodule',
      './fake-drupal/themes/prague',
    );
  }

}
