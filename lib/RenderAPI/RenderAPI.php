<?php

namespace RenderAPI;

use FakeDrupal\FakeDrupal;
use Silex\Application;

class RenderAPI {

  /**
   * Render factory method.
   *
   * @param mixed
   *   The first parameter could be either the classname (if creating a
   *   RenderableBuilder object) or an array of RenderableBuilder objects (if creating
   *   a RenderableBuilderCollection object).
   *
   * @param mixed
   *   The second parameter could either be the array of arguments (if creating a
   *   RenderableBuilder object) or the weight (if creating a
   *   RenderableBuilderCollection object).
   *
   * @param mixed
   *   The third parameter is the weight if creating a RenderableBuilder object.
   *
   * @return Will either return a RenderableBuilder or a
   * RenderableBuilderCollection depending on arguments.
   */
  public static function create() {
    $args = func_get_args();
    // If the first argument is a string, this follows what we'd expect for
    // a RenderableBuilder.
    if (is_string($args[0])) {
      $class = $args[0];
      $params = isset($args[1]) ? $args[1] : array();
      $weight = isset($args[2]) ? $args[2] : 0;
      return new RenderableBuilder($class, $params, $weight);
    }
    // If the first argument is an array, this follows what we'd expect for
    // a RenderableBuilderCollection.
    elseif (is_array($args[0])) {
      $params = $args[0];
      $weight = isset($args[1]) ? $args[1] : 0;
      return new RenderableBuilderCollection($params, $weight);
    }
    else {
      return NULL;
    }
  }

  public static function setApp(Application $app = NULL) {
    static $_app;
    if (isset($app)) {
      $_app = $app;
    }
    return $_app;
  }

  public static function getApp() {
    return RenderAPI::setApp();
  }

  public static function alter(RenderableBuilderInterface $builder) {
    // Builder model: Call any altering functions.
    foreach (FakeDrupal::getAlterCallbacks($builder) as $alterCallback) {
      // Alter callbacks receive the RenderableBuilder, can call methods, and
      // change build class.
      $alterCallback($builder);
    }
  }

  public static function decorate(RenderableInterface $renderable) {
    // Decorator model. Given some registry, decorate the renderable via
    // applicable modules.
    foreach (FakeDrupal::getModuleDecoratorClasses($renderable) as $moduleDecoratorClass) {
      $renderable = new $moduleDecoratorClass($renderable);
    }

    // Decorator model. Given some registry, decorate the renderable via the
    // theme.
    if ($themeDecoratorClass = FakeDrupal::getThemeDecoratorClass($renderable)) {
      $renderable = new $themeDecoratorClass($renderable);
    }
  }

  public static function renderFromTemplate($renderable) {
    if (!$renderable->isTemplateNameSet()) {
      // throw new Exception('No templateName defined!');
      die('No templateName defined in ' . get_class($renderable) . '!');
    }
    $template = $renderable->getTemplateName()   . '.html.twig';
    $vars = $renderable->getAll();
    return RenderAPI::renderTwigTemplate($template, $vars);
  }

  private static function renderTwigTemplate($template, $vars) {
    $app = RenderAPI::getApp();
    if (!isset($app)) {
      die('No application defined!');
    }
    // Check if template exists?
    $exists = FALSE;
    foreach (FakeDrupal::getTwigThemeDirectories() as $dir) {
      if (file_exists($dir . '/' . $template)) {
        $exists = TRUE;
        break;
      }
    }
    if (!$exists) {
      die('File ' . $template . ' not found!');
    }
    return $app['twig']->render($template, $vars);
  }

}
