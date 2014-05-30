<?php

namespace FakeDrupal;

use RenderAPI\RenderManagerInterface;
use RenderAPI\RenderableBuilderInterface;
use RenderAPI\RenderableInterface;

class FakeDrupalRenderManager implements RenderManagerInterface {

  public function alter(RenderableBuilderInterface $builder) {
    // Builder model: Call any altering functions.
    foreach (FakeDrupal::getAlterCallbacks($builder) as $alterCallback) {
      // Alter callbacks receive the RenderableBuilder, can call methods, and
      // change build class.
      $alterCallback($builder);
    }
  }

  public function decorate(RenderableInterface $renderable) {
    foreach (FakeDrupal::getDecoratorClasses($renderable) as $decoratorClass) {
      $renderable = new $decoratorClass($renderable);
    }
    return $renderable;
  }

  public function templateExists(RenderableInterface $renderable) {
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

}
