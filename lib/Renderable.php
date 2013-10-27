<?php

/**
 * @file A renderable object.
 *
 * Represents the runtime content and execution of a render array.
 */
abstract class Renderable {

  // Container for the variables that the renderable will store.
  private $parameters = array();

  // Whether the template variables have been prepared or not.
  private $prepared = FALSE;

  // Whether the prepare function is being run so that we do not re-run the
  // prepare function.
  private $preparing = FALSE;

  // Receives an array of parameters to store.
  function __construct($parameters) {
    foreach ($parameters as $name => $value) {
      $this->set($name, $value);
    }
  }

  // Sets a parameter.
  public function set($name, $value) {
    $this->parameters[$name] = $value;
  }

  // Whether the named parameter exists.
  public function exists($name) {
    return isset($this->parameters[$name]);
  }

  // Returns a parameter by name.
  public function get($name) {
    if ($this->exists($name)) {
      return $this->parameters[$name];
    }
    else {
      // Since this needs to implement a drillable structure, attempt to prepare
      // the variables if they are not yet prepared.
      $this->prepareOnce();
      return $this->exists($name) ? $this->parameters[$name] : NULL;
    }
  }

  // Return all parameters.
  public function getAll() {
    return $this->parameters;
  }

  // Whether this renderable has been prepared.
  public function isPrepared() {
    return $this->prepared;
  }

  // Returns the called class of this renderable.
  public function getBuildClass() {
    return get_called_class();
  }

  // Prepare the variables only if they are not yet prepared
  // (or being prepared).
  protected function prepareOnce() {
    if (!$this->prepared && !$this->preparing) {
      $this->preparing = TRUE;
      $this->prepare();
      $this->preparing = FALSE;
      $this->prepared = TRUE;
    }
  }

  // Invoke the given template and render. Will later depend on some theme
  // engine. @todo Dependency inject.
  public function render() {
    // Prepare variables.
    $this->prepareOnce();

    $template = $this->getRegisteredTemplate();

    extract($this->getAll(), EXTR_SKIP);

    // Start output buffering.
    ob_start();

    // Include the template file.
    include $template;

    // End buffering and return its contents.
    return ob_get_clean();
  }

  // Casting to string invokes render().
  function __tostring() {
    return $this->render();
  }

}
