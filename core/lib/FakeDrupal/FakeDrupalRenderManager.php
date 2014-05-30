<?php

namespace FakeDrupal;

use RenderAPI\RenderAPI;
use RenderAPI\RenderManager;
use RenderAPI\RenderableBuilderInterface;
use RenderAPI\RenderableInterface;

class FakeDrupalRenderManager extends RenderManager {

  public function alter(RenderableBuilderInterface $builder) {
    // Builder model: Call any altering functions.
    foreach ($this->getAlterCallbacks($builder) as $alterCallback) {
      // Alter callbacks receive the RenderableBuilder, can call methods, and
      // change build class.
      $alterCallback($builder);
    }
  }

  public function decorate(RenderableInterface $renderable) {
    foreach ($this->getDecoratorClasses($renderable) as $decoratorClass) {
      $renderable = new $decoratorClass($renderable);
    }
    return $renderable;
  }

  public function baseTemplateExists(RenderableInterface $renderable) {
    $classFile = $renderable->getBuildClass() . '.php';
    $themeEngine = RenderAPI::getThemeEngine();
    $template = $renderable->getTemplateName() . $themeEngine::FILENAME_EXTENSION;
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      foreach (FakeDrupal::getExtensionFiles($extensionName) as $file) {
        if ($classFile === $file) {
          // Make sure template file exists for path.
          return file_exists(FakeDrupal::getExtensionPath($extensionName) . '/templates/' . $template);
        }
      }
    }
    return FALSE;
  }

  public function getTemplateDirectory(RenderableInterface $renderable) {
    $themeEngine = RenderAPI::getThemeEngine();
    foreach ($this->getWeightedTemplateDirectories() as $templateDirectory) {
      if (file_exists($templateDirectory . '/' . $renderable->getTemplateName() . $themeEngine::FILENAME_EXTENSION)) {
        return $templateDirectory;
      }
    }
  }

  /**
   * Call out to alter hooks.
   */
  public function getAlterCallbacks(RenderableBuilderInterface $builder) {
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
  public function getDecoratorClasses(RenderableInterface $renderable) {
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
   * Returns a fake ranking of directories in which to look for templates.
   */
  public function getWeightedTemplateDirectories() {
    $directories = array();
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      $directories[] = FakeDrupal::getExtensionPath($extensionName) . '/templates';
    }
    return array_reverse($directories);
  }

}
