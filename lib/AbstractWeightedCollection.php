<?php

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
   * The weight of the given colleciton.
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
  public function sortParameters() {
    if (!isset($this->parameters_sorted)) {
      $this->parameters_sorted = $this->parameters;
      $sortable = FALSE;
      foreach ($this->parameters_sorted as $key => $parameter) {
        if ($parameter instanceOf WeightedInterface && $parameter->isWeighted()) {
          $sortable = TRUE;
          break;
        }
      }
      if ($sortable) {
        uasort($this->parameters_sorted, array('AbstractWeightedCollection', 'uasort'));
      }
    }
  }

  /**
   * Callback for uasort().
   *
   * @see AbstractWeightedCollection::sortParameters().
   * @return int
   */
  static public function uasort($a, $b) {
    $a_weight = $a instanceOf WeightedInterface ? $a->getWeight() : 0;
    $b_weight = $b instanceOf WeightedInterface ? $b->getWeight() : 0;
    return ($a_weight == $b_weight) ? 0 : (($a_weight < $b_weight) ? -1 : 1);
  }

}
