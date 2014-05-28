<?php

namespace RenderAPI;

/**
 * @file Collection of AbstractRenderable (or subclasses).
 */
class RenderableCollection extends AbstractWeightedCollection {

  /**
   * Simply cast all parameters to strings and concatenate.
   *
   * @return string
   */
  function __tostring() {
    $return = '';
    foreach ($this->getAll() as $parameter) {
      $return .= (string) $parameter;
    }
    return $return;
  }

}
