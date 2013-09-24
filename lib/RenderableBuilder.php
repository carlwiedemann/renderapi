<?php

/**
 * Build a thing we can render. Essentially is the equivalent of the storage
 * component of a render array.
 */
class RenderableBuilder {

  private $buildClasses = array();
  private $buildClass;
  private $params = array();
  private $parsed_params = array();

  // Provide initial build class and parameters.
  function __construct($buildClass, $params) {
    $this->setOriginalBuildClass($buildClass);
    $this->setBuildClass($buildClass);
    foreach ($params as $name => $value) {
      $this->setParam($name, $value);
    }
  }

  // Generalized setters and getters for basic params.
  function setParam($name, $value) {
    $this->params[$name] = $value;
  }

  function getParam($name) {
    return $this->params[$name];
  }

  function getParams() {
    return $this->params;
  }

  function setParsedParams($parsed_params) {
    $this->parsed_params = $parsed_params;
  }

  function getParsedParams() {
    return $this->parsed_params;
  }

  // Finalized class.
  function setBuildClass($buildClass) {
    $this->buildClass = $buildClass;
    $this->buildClasses[] = $buildClass;
  }

  function getBuildClass() {
    return $this->buildClass;
  }

  function getBuildClasses() {
    return $this->buildClasses;
  }

  function setOriginalBuildClass($buildClass) {
    $this->originalBuildClass = $buildClass;
  }

  function getOriginalBuildClass() {
    return $this->originalBuildClass;
  }

  // Parse given parameters as built subclasses.
  static function parseParams($params) {
    $parsed_params = array();
    foreach ($params as $key => $value) {
      if ($value instanceOf RenderableBuilder) {
        $parsed_params[$key] = $value->create();
      }
      else {
        $parsed_params[$key] = $value;
      }
    }
    return $parsed_params;
  }

  // Build the subclassed instance.
  function create() {
    // Create any params as necessary.
    $this->setParsedParams(RenderableBuilder::parseParams($this->getParams()));
    $buildClass = $this->getBuildClass();
    return new $buildClass($this->getParsedParams(), $this->getBuildClasses(), $this->getOriginalBuildClass());
  }
}
