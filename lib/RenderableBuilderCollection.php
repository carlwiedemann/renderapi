<?php

class RenderableBuilderCollection {
  private $parameters;

  private $parameters_sorted;

  function __construct($parameters, $weight = NULL) {
    $this->parameters = $parameters;
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

  public function get($key) {
    return $this->parameters[$key];
  }

  public function getAll() {
    return $this->parameters;
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
        if (($parameter instanceOf RenderableBuilder || $parameter instanceOf RenderableBuilderCollection) && $parameter->isWeighted()) {
          $sortable = TRUE;
          break;
        }
      }
      if ($sortable) {
        uasort($this->parameters_sorted, array('RenderableBuilderCollection', 'uasort'));
      }
    }
  }

  static public function uasort($a, $b) {
    $a_weight = 0;
    $b_weight = 0;
    if ($a instanceOf RenderableBuilder || $a instanceOf RenderableBuilderCollection) {
      $a_weight = $a->getWeight();
    }
    if ($b instanceOf RenderableBuilder || $b instanceOf RenderableBuilderCollection) {
      $b_weight = $b->getWeight();
    }
    if ($a_weight == $b_weight) {
      return 0;
    }
    return ($a_weight < $b_weight) ? -1 : 1;
  }

  public function set($key, $value) {
    $this->parameters[$key] = $value;
  }

  public function exists($key) {
    return isset($this->parameters[$key]);
  }

  public function find($key) {
    if (!$this->exists($key)) {
      // If this doesn't exist, assume it will be invoked in the preprocessor.
      // Therefore, create the renderable. It is feasible that the renderable
      // *could* be statically cached as a property of the instance for
      // performance reasons.
      $renderable = RenderableBuilder::create($this);
      $return = $renderable->get($key);
    }
    else {
      $return = $this->get($key);
    }
    return $return;
  }

  // Casting the Builder to a string creates the Renderable and returns it
  // as a string.
  function __toString() {
    return (string) RenderableBuilder::create($this);
  }

}
