<?php

namespace RenderAPI;

/**
 * @file A builder class to generate a renderable object.
 *
 * Essentially is the equivalent of the storage component of a D8 render array.
 */
class RenderableBuilder extends AbstractWeightedCollection implements RenderableBuilderInterface {
  use RenderableBuilderTrait;

  /**
   * Class name of the eventually built renderable.
   *
   * @var string
   */
  private $buildClass;

  /**
   * Provide initial build class and parameters.
   *
   * @param string $buildClass
   * @param array $parameters
   * @param integer $weight
   * @return void
   */
  function __construct($buildClass, Array $parameters = array(), $weight = 0) {
    parent::__construct($parameters, $weight);
    $this->setBuildClass($buildClass);
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

}
