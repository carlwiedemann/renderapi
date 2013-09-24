<?php

// The thing that is actually rendered, the equivalent of the executed component
// of a render array.
abstract class Renderable implements RenderableInterface {

  private $params = array();
  private $originalBuildClass;
  private $prepared = FALSE;

  function __construct($params, $buildClasses) {
    $this->setOriginalBuildClass($buildClasses[0]);
    $this->setBuildClasses($buildClasses);
    foreach ($params as $name => $value) {
      $this->set($name, $value);
    }
  }

  private function setOriginalBuildClass($buildClass) {
    $this->originalBuildClass = $buildClass;
  }

  private function setBuildClasses($buildClasses) {
    $this->buildClasses = $buildClasses;
  }

  public function getOriginalBuildClass() {
    return $this->originalBuildClass;
  }

  public function getBuildClasses() {
    return $this->buildClasses;
  }

  public function set($name, $value) {
    $this->params[$name] = $value;
  }

  public function get($name) {
    // This needs to implement a drillable structure.
    return $this->$params[$name];
  }

  public function render() {

    $template = getRegistredTemplate($this);

    extract($this->params, EXTR_SKIP);

    // Start output buffering.
    ob_start();

    // Include the template file.
    include $template;

    // End buffering and return its contents.
    return ob_get_clean();
  }

  function __tostring() {
    $this->prepare();
    return $this->render();
  }

}
