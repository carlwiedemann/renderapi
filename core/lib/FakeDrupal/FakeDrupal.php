<?php

/**
 * @file I am fake Drupal and I need some love yes?
 */

namespace FakeDrupal;

use RenderAPI\RenderableBuilderInterface;
use RenderAPI\RenderableInterface;

class FakeDrupal {

  private static $requiredModules = array(
    'node',
    'system',
  );
  private static $enabledModules;
  private static $enabledThemes;
  private static $extensionFileRegistry;
  private static $extensionPathRegistry;

  public static function setEnabledModules(Array $enabledModules) {
    static::$enabledModules = $enabledModules;
  }

  public static function getEnabledModules() {
    return array_merge(static::$requiredModules, static::$enabledModules);
  }

  public static function setEnabledThemes(Array $enabledThemes) {
    static::$enabledThemes = $enabledThemes;
  }

  public static function getEnabledThemes() {
    return static::$enabledThemes;
  }

  public static function getEnabledExtensions() {
    return array_merge(FakeDrupal::getEnabledModules(), FakeDrupal::getEnabledThemes());
  }

  public static function getExtensionPathRegistry() {
    $registry = (object) array();
    foreach (static::$requiredModules as $moduleName) {
      $registry->$moduleName = './core/modules/' . $moduleName;
    }
    foreach (static::$enabledThemes as $themeName) {
      $registry->$themeName = './themes/' . $themeName;
    }
    foreach (static::$enabledModules as $moduleName) {
      $registry->$moduleName = './modules/' . $moduleName;
    }
    return $registry;
  }

  public static function getExtensionFileRegistry() {
    if (!isset(static::$extensionFileRegistry)) {
      static::$extensionFileRegistry = (object) array();
      foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
        static::$extensionFileRegistry->$extensionName = array();
        if ($h = opendir(FakeDrupal::getExtensionPath($extensionName))) {
          while (FALSE !== ($entry = readdir($h))) {
            static::$extensionFileRegistry->{$extensionName}[] = $entry;
          }
          closedir($h);
        }
      }
    }
    return static::$extensionFileRegistry;
  }

  public static function getExtensionFiles($extensionName) {
    $registry = FakeDrupal::getExtensionFileRegistry();
    return $registry->$extensionName;
  }

  public static function getExtensionPath($extensionName) {
    $registry = FakeDrupal::getExtensionPathRegistry();
    return $registry->$extensionName;
  }

  /**
   * Include PHP files.
   */
  public static function bootstrap() {
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      foreach (FakeDrupal::getExtensionFiles($extensionName) as $file) {
        if (preg_match('/.php$/', $file) === 1) {
          include_once FakeDrupal::getExtensionPath($extensionName) . '/' . $file;
        }
      }
    }
  }

  /**
   * Call out to alter hooks.
   */
  public static function getAlterCallbacks(RenderableBuilderInterface $builder) {
    $callbacks = array();
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      $hook_name = str_replace('Theme', $extensionName . '_alter_', $builder->getBuildClass());
      if (function_exists($hook_name)) {
        $callbacks[] = $hook_name;
      }
    }
    return $callbacks;
  }

  /**
   * Classes used to decorate the given Renderable.
   */
  public static function getDecoratorClasses(RenderableInterface $renderable) {
    $classes = array();
    $suffix = str_replace('Theme', '' , $renderable->getBuildClass() . 'Decorator.php');
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      foreach (FakeDrupal::getExtensionFiles($extensionName) as $entry) {
        if (FALSE !== strpos($entry, $suffix)) {
          $classes[] = str_replace('.php', '', $entry);
        }
      }
    }
    return $classes;
  }

  /**
   * Whether the template file exists for the given Renderable.
   */
  public static function templateExists(RenderableInterface $renderable) {
    $classFile = $renderable->getBuildClass() . '.php';
    $template = $renderable->getTemplateName() . '.html.twig';
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      foreach (FakeDrupal::getExtensionFiles($extensionName) as $file) {
        if ($classFile === $file) {
          // Make sure template file exists for path.
          return file_exists(FakeDrupal::getExtensionPath($extensionName) . '/' . $template);
        }
      }
    }
    return FALSE;
  }

  /**
   * Returns a fake ranking of directories in which to look for templates.
   */
  public static function getWeightedTemplateDirectories() {
    $directories = array();
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      $directories[] = FakeDrupal::getExtensionPath($extensionName);
    }
    return array_reverse($directories);
  }

}
