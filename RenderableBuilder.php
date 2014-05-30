<?php

namespace RenderAPI;

/**
 * @file A builder class to generate a renderable object.
 *
 * Essentially is the equivalent of the storage component of a D8 render array.
 */
class RenderableBuilder extends AbstractCollection implements RenderableBuilderInterface {

  /**
   * Class name of the eventually built renderable.
   *
   * @var string
   */
  private $buildClass;

  /**
   * The renderable this represents. This is stored for later access.
   *
   * @var AbstractRenderable
   */
  private $renderable;

  /**
   * Given weight of the builder.
   *
   * @var boolean
   */
  private $weight;

  /**
   * Provide initial build class and parameters.
   *
   * @param string $buildClass
   * @param array $parameters
   * @param integer $weight
   * @return void
   */
  function __construct($buildClass, Array $parameters = array(), $weight = 0) {
    $this->setBuildClass($buildClass);
    $this->parameters = $parameters;
    $this->setWeight($weight);
  }

  /**
   * @param integer $weight
   * @return void
   */
  public function setWeight($weight) {
    $this->weight = (int) $weight;
  }

  /**
   * @return integer
   */
  public function getWeight() {
    return $this->weight;
  }

  /**
   * @return boolean
   */
  public function isWeighted() {
    return $this->weight !== 0;
  }

  /**
   * Suppose we have a separate method similar to get() that is used
   * exclusively via the them layer. Consider whether there should exist a
   * global constraint.
   *
   * @param string $key
   * @return mixed
   */
  public function find($key) {
    $return = NULL;

    if ($this->exists($key)) {
      $return = $this->get($key);
    }
    else {
      if (!isset($this->renderable)) {
        // If this doesn't exist, assume it will be invoked in the preprocessor.
        // Therefore, create the renderable. It is feasible that the renderable
        // *could* be statically cached as a property of the instance for
        // performance reasons.
        $this->renderable = RenderableBuilder::create($this);
      }
      $return = $this->renderable->get($key);
    }

    return $return;
  }

  /**
   * @param string
   * @return void
   */
  public function setBuildClass($buildClass) {
    $this->buildClass = $buildClass;
  }

  /**
   * @return string
   */
  public function getBuildClass() {
    return $this->buildClass;
  }

  /**
   * Factory to build the subclassed instance.
   *
   * @param mixed
   * @return mixed
   */
  public static function create($builder) {

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

      RenderAPI::getRenderManager()->alter($builder);

      // Build the renderable based on the parsed parameters.
      $buildClass = $builder->getBuildClass();
      $renderable = new $buildClass($builder->getAll());

      $renderable = RenderAPI::getRenderManager()->decorate($renderable);

      $return = $renderable;
    }

    return $return;
  }

  /**
   * @return string
   */
  public function render() {
    return RenderableBuilder::create($this)->render();
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->render();
  }

}
