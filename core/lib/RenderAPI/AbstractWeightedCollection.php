<?php

namespace RenderAPI;

/**
 * @file AbstractWeightedCollection.
 */
abstract class AbstractWeightedCollection implements WeightedInterface {

  /**
   * The given parameters for the collection.
   *
   * @var array
   */
  protected $parameters;

  /**
   * The sorted parameters.
   *
   * @var array
   */
  protected $parameters_sorted;

  /**
   * The weight of the given collection.
   *
   * @var integer
   */
  protected $weight;

  /**
   * Constructed for weighted collection.
   *
   * @param array $parameters
   * @param integer $weight
   * @return void
   */
  function __construct(Array $parameters, $weight = 0) {
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
    return $this->getWeight() !== 0;
  }

  /**
   * @param string $key
   * @return mixed
   */
  public function get($key) {
    return isset($this->parameters[$key]) ? $this->parameters[$key] : NULL;
  }

  /**
   * @return array
   */
  public function getAll() {
    return $this->parameters;
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return mixed
   */
  public function set($key, $value) {
    $this->parameters[$key] = $value;
  }

  /**
   * @return boolean
   */
  public function exists($key) {
    return isset($this->parameters[$key]);
  }

  /**
   * @return array
   */
  public function getAllByWeight() {
    $this->sortParameters();
    return $this->parameters_sorted;
  }

  /**
   * For the given parameters, sort them into a new private variable by weight
   * if they implement WeightedInterface.
   *
   * @return void
   */
  protected function sortParameters() {
    if (!isset($this->parameters_sorted)) {
      $sortable = FALSE;
      foreach ($this->parameters as $parameter) {
        if ($parameter instanceOf WeightedInterface && $parameter->isWeighted()) {
          $sortable = TRUE;
          break;
        }
      }
      // This will (purposively) disassciociate the keys.
      $this->parameters_sorted = array_values($this->parameters);
      if ($sortable) {
        usort($this->parameters_sorted, function($a, $b) {
          $a_weight = $a instanceOf WeightedInterface ? $a->getWeight() : 0;
          $b_weight = $b instanceOf WeightedInterface ? $b->getWeight() : 0;
          return ($a_weight == $b_weight) ? 0 : (($a_weight < $b_weight) ? -1 : 1);
        });
      }
    }
  }

}
