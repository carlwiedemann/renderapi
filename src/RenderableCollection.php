<?php

namespace RenderAPI;

/**
 * @file Collection of AbstractRenderable (or subclasses).
 */
class RenderableCollection extends AbstractWeightedCollection implements RenderableInterface {

  /**
   * Prepare all parameters.
   */
  public function prepare() {
    foreach ($this->getAll() as $value) {
      if ($value instanceOf RenderableInterface) {
        $value->prepare();
      }
    }
  }

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
