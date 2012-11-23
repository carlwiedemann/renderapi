<?php

// namespace RenderAPI\Core;

class RenderableScalar extends RenderableBase {

  public function __construct($arg) {
    $this->value = (string) $arg;
  }

  protected function setValue() {
    // Value is set in the constructor.
  }

}
