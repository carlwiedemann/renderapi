<?php

/**
 * @file A builder class to generate a renderable object.
 *
 * Essentially is the equivalent of the storage component of a D8 render array.
 */
class RenderableBuilder extends AbstractCollection implements WeightedInterface {

  // Class of the eventually built renderable.
  private $buildClass;

  // The renderable this represents.
  private $renderable;

  private $weight;

  // Provide initial build class and parameters.
  function __construct($buildClass, Array $parameters = array(), $weight = 0) {
    $this->setBuildClass($buildClass);
    $this->parameters = $parameters;
    $this->setWeight($weight);
  }

  public function setWeight($weight) {
    $this->weight = (int) $weight;
  }

  public function getWeight() {
    return $this->weight;
  }

  public function isWeighted() {
    return $this->weight !== 0;
  }

  // Suppose we have a separate method similar to get() that is used
  // exclusively via the them layer. Consider whether there should exist a
  // global constraint.
  public function find($key) {
    $return = NULL;
    if ($this->exists($key)) {
      $return = $this->get($key);
    }
    elseif (!isset($this->renderable)) {
      // If this doesn't exist, assume it will be invoked in the preprocessor.
      // Therefore, create the renderable. It is feasible that the renderable
      // *could* be statically cached as a property of the instance for
      // performance reasons.
      $this->renderable = RenderableBuilder::create($this);
    }
    if ($this->renderable->exists($key)) {
      $return = $this->renderable->get($get);
    }
    return $return;
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

      // Build the renderable based on the parsed parameters.
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
