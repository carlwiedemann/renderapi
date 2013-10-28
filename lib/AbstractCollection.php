<?php

abstract class AbstractCollection {
  private $parameters;

  function __construct($parameters) {
    $this->parameters = $parameters;
  }

  public function get($key) {
    return $this->parameters[$key];
  }

  public function getAll() {
    return $this->parameters;
  }

  public function set($key, $value) {
    $this->parameters[$key] = $value;
  }

  public function exists($key) {
    return isset($this->parameters[$key]);
  }
}
