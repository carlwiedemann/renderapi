<?php

namespace RenderAPI;

/**
 * @file Collection of AbstractRenderable (or subclasses).
 */
class RenderableCollection extends AbstractWeightedCollection implements RenderableInterface {

  /**
   * Concatenates output of sorted parameters.
   *
   * @return string
   */
  public function render() {
    $output = '';
    foreach ($this->getAllByWeight() as $parameter) {
      $output .= (string) $parameter;
    }
    return $output;
  }

  /**
   * Simply cast all parameters to strings and concatenate.
   *
   * @return string
   */
  public function __toString() {
    return $this->render();
  }

}
