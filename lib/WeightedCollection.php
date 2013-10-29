<?php

class WeightedCollection extends AbstractCollection implements WeightedInterface {
  protected $parameters_sorted;

  protected $parameters;

  protected $weight = 0;

  function __construct($parameters = array(), $weight = NULL) {
    $this->parameters = $parameters;
    if (isset($weight)) {
      $this->setWeight($weight);
    }
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

  public function getAllByWeight() {
    $this->sortParameters();
    return $this->parameters_sorted;
  }

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
        uasort($this->parameters_sorted, array('WeightedCollection', 'uasort'));
      }
    }
  }

  static public function uasort($a, $b) {
    $a_weight = $a instanceOf WeightedInterface ? $a->getWeight() : 0;
    $b_weight = $b instanceOf WeightedInterface ? $b->getWeight() : 0;
    return ($a_weight == $b_weight) ? 0 : (($a_weight < $b_weight) ? -1 : 1);
  }

}
