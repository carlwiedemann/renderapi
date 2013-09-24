<?php

/**
 * @file Decorator class to allow modules and themes to alter parameters for
 * templates.
 */

abstract class RenderableDecorator extends Renderable {
  private $renderable;

  // Set child.
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

  function getRegisteredTemplate() {
    return $this->renderable->getRegisteredTemplate();
  }

  // Delegate to child.
  public function render() {

    $template = $this->getRegisteredTemplate();

    extract($this->getParams(), EXTR_SKIP);

    // Start output buffering.
    ob_start();

    // Include the template file.
    include $template;

    // End buffering and return its contents.
    return ob_get_clean();
  }

}
