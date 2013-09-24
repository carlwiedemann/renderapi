<?php

/**
 * @file The Renderable class represents the data component of a render array.
 */
abstract class Renderable {

  private $params = array();
  private $buildClass; 

  function __construct($params, $buildClasses) {
    foreach ($buildClasses as $buildClass) {
      $this->setBuildClass($buildClass);
    }
    foreach ($params as $name => $value) {
      $this->set($name, $value);
    }
  }

  public function setBuildClass($buildClass) {
    $this->buildClass = $buildClass;
    $this->buildClasses[] = $buildClass;
  }

  function getBuildClass() {
    return $this->buildClass;
  }

  public function getBuildClasses() {
    return $this->buildClasses;
  }

  public function set($name, $value) {
    $this->params[$name] = $value;
  }

  public function get($name) {
    // @todo This needs to implement a drillable structure.
    return $this->params[$name];
  }

  public function getParams() {
    return $this->params;
  }

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

  function __tostring() {
    $this->prepare();
    return $this->render();
  }

}
