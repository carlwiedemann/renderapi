<?php

abstract class RenderableDecorator implements RenderableInterface {
  // Set child.
  function __construct($renderable) {
    $this->renderable = $renderable;
  }

  // Overload methods from renderable.
  function __call($method, $arguments) {
    return $this->renderable->$method($arguments);
  }
}
