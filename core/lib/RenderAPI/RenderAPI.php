<?php

namespace RenderAPI;

class RenderAPI {

  /**
   * The theme engine being used.
   */
  protected static $themeEngine;

  /**
   * The render manager being used.
   */
  protected static $renderManager;

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
   * @param $themeEngine ThemeEngineInterface
   */
  public static function setThemeEngine(ThemeEngineInterface $themeEngine) {
    static::$themeEngine = $themeEngine;
  }

  /**
   * @return ThemeEngineInterface
   */
  public static function getThemeEngine() {
    if (!isset(static::$themeEngine)) {
      RenderAPI::setThemeEngine(new ThemeEngine());
    }
    return static::$themeEngine;
  }

  /**
   * @param $renderManager RenderManagerInterface
   */
  public static function setRenderManager(RenderManagerInterface $renderManager) {
    static::$renderManager = $renderManager;
  }

  /**
   * @return RenderManagerInterface
   */
  public static function getRenderManager() {
    if (!isset(static::$renderManager)) {
      RenderAPI::setRenderManager(new RenderManager());
    }
    return static::$renderManager;
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

    $themeEngine = RenderAPI::getThemeEngine();

    if (!RenderAPI::getRenderManager()->baseTemplateExists($renderable)) {
      // throw new Exception('File ' . $template . ' not found!');
      die('Base template ' . $renderable->getTemplateName() . $themeEngine::FILENAME_EXTENSION . ' not found!');
    }

    return $themeEngine->render($renderable);
  }

  /**
   * Factory to build the subclassed instance.
   *
   * @param mixed
   * @return mixed
   */
  public static function createRenderable(RenderableBuilderInterface $builder) {

    if (!$builder->renderableBuilt()) {
      if ($builder instanceOf RenderableBuilderCollection) {
        $parameters = array();
        // RenderableCollections contain Renderables.
        foreach ($builder->getAll() as $key => $value) {
          $parameters[$key] = ($value instanceOf RenderableBuilderInterface) ? $value->getRenderable() : $value;
        }
        $builder->setRenderable(new RenderableCollection($parameters));
      }
      elseif ($builder instanceOf RenderableBuilder) {

        RenderAPI::getRenderManager()->alter($builder);

        // Build the renderable based on the parsed parameters.
        $buildClass = $builder->getBuildClass();
        $renderable = new $buildClass($builder->getAll(), $builder->getWeight());

        $renderable = RenderAPI::getRenderManager()->decorate($renderable);

        $builder->setRenderable($renderable);
      }
    }

    return $builder->getRenderable();
  }

}
