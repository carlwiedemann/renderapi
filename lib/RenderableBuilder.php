<?php

/**
 * @file A builder class to generate a renderable object.
 *
 * Essentially is the equivalent of the storage component of a D8 render array.
 */
class RenderableBuilder {

  // Container for the pre-built variables of the builder.
  private $params = array();

  // Class of the eventually built renderable.
  private $buildClass;

  // If this exists in an array structure, allow for a weight parameter.
  private $weight = 0;

  // Whether a weight was set manually or not.
  private $weighted = FALSE;

  // Provide initial build class and parameters.
  function __construct($buildClass, $params, $weight = NULL) {
    $this->setBuildClass($buildClass);
    foreach ($params as $name => $value) {
      $this->set($name, $value);
    }
    if (isset($weight)) {
      $this->weighted = TRUE;
      $this->setWeight($weight);
    }
    else {
      $this->setWeight(0);
    }
  }

  public function setWeight($weight) {
    $this->weight = (int) $weight;
  }

  public function getWeight() {
    return $this->weight;
  }

  function isWeighted() {
    return $this->weighted;
  }

  public function set($name, $value) {
    $this->params[$name] = $value;
  }

  public function exists($name) {
    return isset($this->params[$name]);
  }

  public function get($name) {
    return $this->params[$name];
  }

  public function getAll() {
    return $this->params;
  }

  public function setBuildClass($buildClass) {
    $this->buildClass = $buildClass;
  }

  public function getBuildClass() {
    return $this->buildClass;
  }

  // Factory to build the subclassed instance. The builder in this case may be:
  // * A scalar.
  // * A renderable builder.
  // * An array of either of the above.
  static public function create($builder) {

    $return = NULL;

    if (is_scalar($builder)) {
      $return = $builder;
    }
    elseif (is_array($builder)) {
      $return = array();
      foreach ($builder as $key => $value) {
        $return[$key] = RenderableBuilder::create($value);
      }
    }
    elseif ($builder instanceOf RenderableBuilder) {
      // Builder model: Call any altering functions.
      foreach (getAlterCallbacks($builder) as $alterCallback) {
        // Alter callbacks receive the RenderableBuilder, can call methods, and
        // change build class.
        $alterCallback($builder);
      }

      // Build the renderable based on the parsed params.
      $buildClass = $builder->getBuildClass();
      $renderable = new $buildClass($builder->getAll());

      // Decorator model. Given some registry, decorate the renderable via
      // applicable modules.
      foreach (getModuleDecoratorClasses($renderable) as $moduleDecoratorClass) {
        $renderable = new $moduleDecoratorClass($renderable);
      }

      // Decorator model. Given some registry, decorate the renderable via the
      // theme.
      if ($themeDecoratorClass = getThemeDecoratorClass($renderable)) {
        $renderable = new $themeDecoratorClass($renderable);
      }

      $return = $renderable;
    }

    return $return;
  }

  // Casting the Builder to a string creates the Renderable and returns it
  // as a string.
  function __toString() {
    return (string) RenderableBuilder::create($this);
  }

}
