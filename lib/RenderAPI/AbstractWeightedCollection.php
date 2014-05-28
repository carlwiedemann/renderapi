<?php

namespace RenderAPI;

/**
 * @file AbstractWeightedCollection.
 */
abstract class AbstractWeightedCollection extends AbstractCollection implements WeightedInterface {

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
   * @param int $weight
   * @return void
   */
  function __construct(Array $parameters, $weight = 0) {
    $this->parameters = $parameters;
    $this->setWeight($weight);
  }

  /**
   * @param int $weight
   * @return void
   */
  public function setWeight($weight) {
    $this->weight = (int) $weight;
  }

  /**
   * @return int
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
      $this->parameters_sorted = $this->parameters;
      if ($sortable) {
        if (AbstractWeightedCollection::isAssociative($this->parameters)) {
          uasort($this->parameters_sorted, array('AbstractWeightedCollection', 'sort'));
        }
        else {
          usort($this->parameters_sorted, array('AbstractWeightedCollection', 'sort'));
        }
      }
    }
  }

  /**
   * Callback for usort().
   *
   * @see AbstractWeightedCollection::sortParameters().
   * @return int
   */
  static public function sort($a, $b) {
    $a_weight = $a instanceOf WeightedInterface ? $a->getWeight() : 0;
    $b_weight = $b instanceOf WeightedInterface ? $b->getWeight() : 0;
    return ($a_weight == $b_weight) ? 0 : (($a_weight < $b_weight) ? -1 : 1);
  }

  /**
   * Check whether a given array is associative or not.
   *
   * @param array $array
   * @return boolean
   */
  static public function isAssociative($array) {
    for ($k = 0, reset($array) ; $k === key($array) ; next($array)) ++$k;
    return $k !== count($array);
  }

}
