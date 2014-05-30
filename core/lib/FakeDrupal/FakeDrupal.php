<?php

/**
 * @file I am fake Drupal and I need some love yes?
 */

namespace FakeDrupal;

use RenderAPI\RenderableBuilderInterface;
use RenderAPI\RenderableInterface;

class FakeDrupal {

  private static $requiredModules = array(
    'system',
    'node',
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

}
