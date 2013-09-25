<?php

/**
 * @file Decorator class to allow modules and themes to alter parameters for
 * templates.
 */
abstract class RenderableDecorator extends Renderable {
  // The child object to decorate.
  private $renderable;

  function __construct($renderable, $buildClass) {
    $this->renderable = $renderable;
    $this->renderable->setBuildClass($buildClass);
  }

  // Delegate to child.
  public function getBuildClasses() {
    return $this->renderable->getBuildClasses();
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
  public function getParams() {
    return $this->renderable->getParams();
  }

  // Delegate to child.
  function prepare() {
    return $this->renderable->prepare();
  }

  // Delegate to child.
  function getRegisteredTemplate() {
    return $this->renderable->getRegisteredTemplate();
  }

}
