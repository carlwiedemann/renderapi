<?php

/**
 * @file Collection of AbstractRenderable (or subclasses).
 */
class RenderableCollection extends AbstractCollection {

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
