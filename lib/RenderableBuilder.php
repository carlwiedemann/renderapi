<?php

/**
 * Build a thing we can render. Essentially is the equivalent of the storage
 * component of a render array.
 */
class RenderableBuilder {

  // Container for the pre-built variables of the builder.
  private $params = array();

  // Class of the eventually built renderable.
  private $buildClass;

  // Store list of classes for later registry checks.
  private $buildClasses = array();

  // Provide initial build class and parameters.
  function __construct($buildClass, $params) {
    $this->setBuildClass($buildClass);
    foreach ($params as $name => $value) {
      $this->set($name, $value);
    }
  }

  function set($name, $value) {
    $this->params[$name] = $value;
  }

  function get($name) {
    return $this->params[$name];
  }

  function getAll() {
    return $this->params;
  }

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

  // Build the subclassed instance.
  function create() {

    // Builder model: Call any altering functions.
    foreach (getAlterCallbacks($this) as $alterCallback) {
      // Alter callbacks receive the RenderableBuilder, can call methods, and
      // change build class.
      $alterCallback($this);
    }

    // Based on the parameters, build the Renderable.
    $buildClass = $this->getBuildClass();

    // Build sub-parameters if they are renderables.
    $parsed_params = array();
    foreach ($this->getAll() as $key => $value) {
      if ($value instanceOf RenderableBuilder) {
        $parsed_params[$key] = $value->create();
      }
      else {
        $parsed_params[$key] = $value;
      }
    }

    // Build the renderable based on the parsed params.
    $renderable = new $buildClass($parsed_params, $this->getBuildClasses());

    // Decorator model. Given some registry, decorate the renderable via
    // applicable modules.
    foreach (getModuleDecoratorClasses($renderable) as $moduleDecoratorClass) {
      $renderable = new $moduleDecoratorClass($renderable, $moduleDecoratorClass);
    }

    // Decorator model. Given some registry, decorate the renderable via the
    // theme.
    if ($themeDecoratorClass = getThemeDecoratorClass($renderable)) {
      $renderable = new $themeDecoratorClass($renderable, $themeDecoratorClass);
    }

    return $renderable;
  }
}
