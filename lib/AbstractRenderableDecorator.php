<?php

/**
 * @file Decorator class to allow modules and themes to alter parameters for
 * templates.
 */
abstract class AbstractRenderableDecorator extends AbstractRenderable {
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
  public function set($key, $value) {
    $this->renderable->set($key, $value);
  }

  // Delegates to child.
  public function get($key) {
    if ($this->exists($key)) {
      return $this->renderable->get($key);
    }
    else {
      $this->prepareOnce();
      return $this->exists($key) ? $this->renderable->get($key) : NULL;
    }
  }

  // Delegates to child.
  public function exists($key) {
    return $this->renderable->exists($key);
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
