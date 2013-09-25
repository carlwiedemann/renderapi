<?php

/**
 * @file A renderable object.
 *
 * Represents the runtime content and execution of a render array.
 */
abstract class Renderable {

  // Container for the variables that the renderable will store.
  private $params = array();

  function __construct($params) {
    foreach ($params as $name => $value) {
      $this->set($name, $value);
    }
  }

  public function set($name, $value) {
    $this->params[$name] = $value;
  }

  public function get($name) {
    // @todo This needs to implement a drillable structure.
    return $this->params[$name];
  }

  function getBuildClass() {
    return get_called_class();
  }

  public function getAll() {
    return $this->params;
  }

  public function prepare() {
    // This is empty since this is an abstract class.
  }

  // Invoke the given template and render. Will later depend on some theme
  // engine.
  public function render() {
    // Prepare variables.
    $this->prepare();

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
