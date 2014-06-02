<?php

namespace RenderAPI;

/**
 * @file Collection of AbstractRenderable (or subclasses).
 */
class RenderableCollection extends AbstractWeightedCollection implements RenderableInterface {

  public function render() {
    $return = '';
    foreach ($this->getAllByWeight() as $parameter) {
      $return .= (string) $parameter;
    }
    return $return;
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
