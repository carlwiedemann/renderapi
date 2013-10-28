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
  function __construct($buildClass, $params = array(), $weight = NULL) {
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

  public function isWeighted() {
    return $this->weighted;
  }

  public function set($name, $value) {
    $this->params[$name] = $value;
  }

  public function exists($name) {
    return isset($this->params[$name]);
  }

  public function get($name) {
    // Consider what constitutes creation of the renderable.

    // The real drillability issue: we may wish to drill into structure
    // that pertains to the finalized renderable state, subject to variables
    // created in the preprocessors, not yet available in the builder.

    // Let's assume that if a parameter exists, we'll use it, and if it doesn't
    // exist, we'll delegate to the finalized Renderable (the object of type
    // $this->buildClass). There are some quetsions here in terms of execution
    // order, that is, if this should be delegated prior to the invocation of
    // the theme layer itself.

    // It may make sense to have a check whether the implementor can dig into
    // the structure if it is coming from the theme layer, or a separate method
    // altogether. @see find().
    return $this->params[$name];
  }

  // Suppose we have a separate method similar to get() that is used
  // exclusively via the them layer. Consider whether there should exist a
  // global constraint.
  public function find($name) {
    if (!$this->exists($name)) {
      // If this doesn't exist, assume it will be invoked in the preprocessor.
      // Therefore, create the renderable. It is feasible that the renderable
      // *could* be statically cached as a property of the instance for
      // performance reasons.
      $renderable = RenderableBuilder::create($this);
      $return = $renderable->get($name);
    }
    else {
      $return = $this->get($name);
    }
    return $return;
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
  // * A RenderableBuilder object.
  // * An array of either of the above.
  static public function create($builder) {

    $return = NULL;

    if (is_scalar($builder)) {
      $return = $builder;
    }
    elseif ($builder instanceOf RenderableBuilderCollection) {
      $parameters = array();
      foreach ($builder->getAllByWeight() as $key => $value) {
        $parameters[$key] = RenderableBuilder::create($value);
      }
      $return = new RenderableCollection($parameters);
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
