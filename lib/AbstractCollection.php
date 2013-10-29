<?php

abstract class AbstractCollection {

  /**
   * @var array
   */
  protected $parameters;

  /**
   * Set initial parameters array.
   *
   * @param array $parameters
   */
  function __construct(Array $parameters) {
    $this->parameters = $parameters;
  }

  /**
   * @param string $key
   * @return mixed
   */
  public function get($key) {
    return $this->parameters[$key];
  }

  /**
   * @return array
   */
  public function getAll() {
    return $this->parameters;
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return mixed
   */
  public function set($key, $value) {
    $this->parameters[$key] = $value;
  }

  /**
   * @return boolean
   */
  public function exists($key) {
    return isset($this->parameters[$key]);
  }
}
