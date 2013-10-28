<?php

class RenderableCollection extends AbstractCollection {
  // Simply cast all parameters to strings and concatenate.
  function __tostring() {
    $return = '';
    foreach ($this->getAll() as $parameter) {
      $return .= (string) $parameter;
    }
    return $return;
  }
}
