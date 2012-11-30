<?php

// namespace RenderAPI\Core;

/**
 * An array of renderables, inspired by Attribute.php
 */
class RenderableCollection extends RenderableBase implements \ArrayAccess, \IteratorAggregate {

  public $inner = array();

  /**
   * Implements ArrayAccess::offsetSet().
   */
  function offsetSet($offset, $value) {
    if ($this->validKey($offset)) {
      if (isset($offset)) {
        $this->inner[$offset] = $value;
      }
      else {
        $this->inner[] = $value;
      }
    }
  }

  /**
   * Implements ArrayAccess::offsetUnset().
   */
  function offsetUnset($offset) {
    unset($this->inner[$offset]);
  }

  /**
   * Implements ArrayAccess::offsetGet().
   */
  function offsetGet($offset) {
    return $this->inner[$offset];
  }

  /**
   * Implements ArrayAccess::offsetExists().
   */
  function offsetExists($offset) {
    return isset($this->inner[$offset]);
  }

  /**
   * Implements IteratorAggregate::getIterator().
   */
  public function getIterator() {
    return new \ArrayIterator($this->inner);
  }

  public function __construct($arg) {
    foreach ($arg as $key => $value) {
      if ($this->validKey($key)) {
        $renderable = RenderableFactory::create($value);
        $this->offsetSet($key, $renderable);
      }
    }
  }

  public function show() {
    $this->printed = FALSE;
    foreach ($this->getIterator() as $key => $value) {
      $value->show();
      $this->offsetSet($key, $value);
    }
  }

  protected function setValue() {
    $this->value = implode($this->inner);
  }

  public function bool() {
    $has_attributes = isset($this->attributes) && !empty($this->attributes);
    $inner_exists = FALSE;
    if (!$has_attributes) {
      foreach ($this->getIterator() as $key => $value) {
        if ($value->bool()) {
          $inner_exists = TRUE;
          break;
        }
      }
    }
    return $has_attributes || $inner_exists;
  }

}
