<?php

/**
 * @file Interface for weighted object.
 */
interface WeightedInterface {

  /**
   * Sets integer weight parameter.
   *
   * @param $integer
   */
  public function setWeight($weight);

  /**
   * Returns the weight of the object.
   */
  public function getWeight();

  /**
   * Determines if the object has been initialized to be weighted.
   */
  public function isWeighted();

}
