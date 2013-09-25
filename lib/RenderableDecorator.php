<?php

/**
 * @file Decorator class to allow modules and themes to alter parameters for
 * templates.
 */
abstract class RenderableDecorator extends Renderable {
  // The child object to decorate.
  private $renderable;

  function __construct($renderable) {
    $this->renderable = $renderable;
  }

  // Delegate to child.
  public function getBuildClass() {
    return $this->renderable->getBuildClass();
  }

  // Delegate to child.
  public function set($name, $value) {
    $this->renderable->set($name, $value);
  }

  // Delegate to child.
  public function get($name) {
    return $this->renderable->get($name);
  }

  // Delegate to child.
  public function getAll() {
    return $this->renderable->getAll();
  }

  // Delegate to child.
  public function prepare() {
    return $this->renderable->prepare();
  }

  // Delegate to child.
  public function getRegisteredTemplate() {
    return $this->renderable->getRegisteredTemplate();
  }

}
