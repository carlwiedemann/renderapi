<?php

/**
 * @file A renderable object.
 *
 * Represents the runtime content and execution of a render array.
 */
abstract class Renderable {

  // Container for the variables that the renderable will store.
  private $params = array();

  private $isPrepared = FALSE;

  function __construct($params) {
    foreach ($params as $name => $value) {
      $this->set($name, $value);
    }
  }

  public function set($name, $value) {
    $this->params[$name] = $value;
  }

  public function exists($name) {
    return isset($this->params[$name]);
  }

  public function get($name) {
    // Since this needs to implement a drillable structure, prepare the variables
    // if they are not prepared.
    if (!$this->isPrepared()) {
      $this->prepare();
    }
    return $this->params[$name];
  }

  public function isPrepared() {
    return $this->isPrepared;
  }

  private function setPrepared() {
    $this->isPrepared = TRUE;
  }

  public function getBuildClass() {
    return get_called_class();
  }

  public function getAll() {
    return $this->params;
  }

  protected function prepare() {
    $this->setPrepared();
  }

  // Invoke the given template and render. Will later depend on some theme
  // engine.
  public function render() {
    // Prepare variables.
    if (!$this->isPrepared()) {
      $this->prepare();
    }

    $template = $this->getRegisteredTemplate();

    extract($this->getAll(), EXTR_SKIP);

    // Start output buffering.
    ob_start();

    // Include the template file.
    include $template;

    // End buffering and return its contents.
    return ob_get_clean();
  }

  // Casting to string invokes render function.
  function __tostring() {
    // Return theme function.
    return $this->render();
  }

}
