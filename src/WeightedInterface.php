<?php

namespace RenderAPI;

/**
 * @file Weighted interface.
 */
interface WeightedInterface {

  /**
   * Sets weight.
   *
   * @param integer $weight
   */
  public function setWeight($weight);

  /**
   * Returns the weight of the object.
   */
  public function getWeight();

  /**
   * Whether this object has a non-zero weight.
   */
  public function isWeighted();
}
