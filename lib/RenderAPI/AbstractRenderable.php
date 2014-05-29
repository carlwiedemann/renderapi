<?php

namespace RenderAPI;

/**
 * @file Abstract class for a Renderable object.
 *
 * Represents the runtime content and execution of a render array.
 */
abstract class AbstractRenderable extends AbstractCollection implements RenderableInterface {

  /**
   * The filename of the template to be used.
   */
  protected $templateName;

  /**
   * Whether the template variables have been prepared or not.
   *
   * @var boolean
   */
  protected $prepared = FALSE;

  /**
   * Whether the prepare function is being run so that we do not re-run the
   * prepare function.
   *
   * @var boolean
   */
  protected $preparing = FALSE;

  /**
   * Returns a parameter by key.
   *
   * @return mixed
   */
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

  /**
   * Whether this renderable has been prepared.
   *
   * @return boolean
   */
  public function isPrepared() {
    return $this->prepared;
  }

  /**
   * Returns the called class of this renderable.
   *
   * @return string
   */
  public function getBuildClass() {
    return get_called_class();
  }

  /**
   * Prepare the variables only if they are not yet prepared
   * (or being prepared).
   *
   * @return void
   */
  protected function prepareOnce() {
    if (!$this->prepared && !$this->preparing) {
      $this->preparing = TRUE;
      $this->prepare();
      $this->preparing = FALSE;
      $this->prepared = TRUE;
    }
  }

  /**
   * Child objects must call prepare.
   *
   * @return void
   */
  abstract public function prepare();

  /**
   * Returns set template name.
   */
  public function getTemplateName() {
    return $this->templateName;
  }

  /**
   * Whether templateName has been set.
   */
  public function isTemplateNameSet() {
    return isset($this->templateName);
  }

  /**
   *  Invoke the given template and render. Will later depend on some theme
   * engine.
   *
   * @return string
   */
  public function render() {
    // Prepare variables.
    $this->prepareOnce();
    return RenderAPI::renderFromTemplate($this);
  }

  /**
   * Cast to string.
   */
  public function __toString() {
    return $this->render();
  }

}
