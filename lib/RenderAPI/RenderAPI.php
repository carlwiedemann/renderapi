<?php

namespace RenderAPI;

use FakeDrupal\FakeDrupal;
use Silex\Application;

class RenderAPI {

  /**
   * The theme engine being used.
   */
  protected static $themeEngine;

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

  /**
   * @param object
   */
  public static function setThemeEngine($themeEngine = NULL) {
    static::$themeEngine = $themeEngine;
  }

  /**
   * @return object
   */
  public static function getThemeEngine() {
    return static::$themeEngine;
  }

  /**
   * @param $builder RenderableBuilderInterface
   * @return void
   */
  public static function alter(RenderableBuilderInterface $builder) {
    // Builder model: Call any altering functions.
    foreach (FakeDrupal::getAlterCallbacks($builder) as $alterCallback) {
      // Alter callbacks receive the RenderableBuilder, can call methods, and
      // change build class.
      $alterCallback($builder);
    }
  }

  /**
   * @param $renderable RenderableInterface
   * @return void
   */
  public static function decorate(RenderableInterface $renderable) {
    foreach (FakeDrupal::getDecoratorClasses($renderable) as $decoratorClass) {
      $renderable = new $decoratorClass($renderable);
    }
    return $renderable;
  }

  /**
   * @param $renderable RenderableInterface
   * @return string
   */
  public static function renderFromTemplate(RenderableInterface $renderable) {
    if (!$renderable->isTemplateNameSet()) {
      // throw new Exception('No templateName defined!');
      die('No templateName defined in ' . get_class($renderable) . '!');
    }

    $template = $renderable->getTemplateName()   . '.html.twig';
    $twig = RenderAPI::getThemeEngine();
    if (!isset($twig)) {
      // throw new Exception('No theme engine defined!');
      die('No theme engine defined!');
    }
    // Check if template exists?
    $exists = FALSE;
    foreach (array_reverse(FakeDrupal::getTemplateDirectories($renderable)) as $dir) {
      if (file_exists($dir . '/' . $template)) {
        $exists = TRUE;
        break;
      }
    }
    if (!$exists) {
      // throw new Exception('File ' . $template . ' not found!');
      die('File ' . $template . ' not found!');
    }
    return $twig->render($template, $renderable->getAll());
  }

}
