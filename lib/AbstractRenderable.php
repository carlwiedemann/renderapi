<?php

/**
 * @file A renderable object.
 *
 * Represents the runtime content and execution of a render array.
 */
abstract class AbstractRenderable extends AbstractCollection {

  // Whether the template variables have been prepared or not.
  protected $prepared = FALSE;

  // Whether the prepare function is being run so that we do not re-run the
  // prepare function.
  protected $preparing = FALSE;

  // Returns a parameter by key.
  public function get($key) {
    if ($this->exists($key)) {
      return $this->parameters[$key];
    }
    else {
      // Since this needs to implement a drillable structure, attempt to prepare
      // the variables if they are not yet prepared.
      $this->prepareOnce();
      return $this->exists($key) ? $this->parameters[$key] : NULL;
    }
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
