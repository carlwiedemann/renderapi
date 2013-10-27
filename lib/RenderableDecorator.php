<?php

/**
 * @file Decorator class to allow modules and themes to alter parameters for
 * templates.
 */
abstract class RenderableDecorator extends Renderable {
  // The child object to decorate.
  private $renderable;

  // Receive the renderable to decorate.
  function __construct($renderable) {
    $this->renderable = $renderable;
  }

  // Delegates to child.
  public function getBuildClass() {
    return $this->renderable->getBuildClass();
  }

  // Delegates to child.
  public function set($name, $value) {
    $this->renderable->set($name, $value);
  }

  // Delegates to child.
  public function get($name) {
    if ($this->exists($name)) {
      return $this->renderable->get($name);
    }
    else {
      $this->prepareOnce();
      return $this->exists($name) ? $this->renderable->get($name) : NULL;
    }
  }

  // Delegates to child.
  public function exists($name) {
    return $this->renderable->exists($name);
  }

  // Delegates to child.
  public function getAll() {
    return $this->renderable->getAll();
  }

  // Delegates to child.
  public function prepare() {
    return $this->renderable->prepare();
  }

  // Delegates to child.
  public function getRegisteredTemplate() {
    return $this->renderable->getRegisteredTemplate();
  }

}
