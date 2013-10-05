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

  // Check for local existence.
  public function get($name) {
    // If we haven't prepared the variables yet, prepare them.
    if (!$this->exists($name) && !$this->isPrepared()) {
      $this->prepare();
      $this->setPrepared();
    }
    return $this->renderable->get($name);
  }

  public function exists($name) {
    return $this->renderable->exists($name);
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
