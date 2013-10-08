<?php

/**
 * @file Accessor class.
 *
 * This likely has a lot of overlap between how this works in the Twig engine
 * as well as Entity API, so these could likely be consolidated in the future.
 *
 * @todo This may be better suited as an abstract factory to decrease method
 * complexity.
 */
class Accessor {
  // The given value to be accessed.
  private $value;

  // Whether the converted state of a RenderableBuilder will be delegated to
  // the base class.
  private $themed;

  function __construct($value, $themed = FALSE) {
    $this->value = $value;
    $this->themed = $themed;
  }

  /**
   * Factory class method.
   */
  static public function create($value, $themed = FALSE) {
    return new Accessor($value, $themed);
  }

  /**
   * Getter function.
   */
  public function get($param) {
    if (is_array($this->value) && isset($this->value[$param])) {
      // If this is a component of an array, recurse.
      return Accessor::create($this->value[$param], $this->themed);
    }
    elseif (is_object($this->value)) {
      // If this is a Renderable or a Renderable builder, call the get() method.
      if ($this->value instanceOf Renderable || $this->value instanceOf RenderableBuilder) {
        // If necessary, create the renderable so that we can access the values.
        if ($this->themed && $this->value instanceOf RenderableBuilder) {
          $value = RenderableBuilder::create($this->value);
        }
        else {
          $value = $this->value;
        }
        return Accessor::create($value->get($param), $this->themed);
      }
      elseif (isset($this->value->$param)) {
        // If this is a struct, treat as much.
        return Accessor::create($this->value->$param, $this->themed);
      }
    }
    else {
      // Return null for scalars and anything else.
      return NULL;
    }
  }

  /**
   * Recursively converts a given value into a something to be sent to a JSON
   * response.
   */
  static public function convert($value, $themed) {
    if (is_object($value) && ($value instanceOf Renderable || $value instanceOf RenderableBuilder)) {
      $return = array();
      // Builders get converted into Renderables.
      if ($themed && $value instanceOf RenderableBuilder) {
        $value = RenderableBuilder::create($value);
        $value->prepare();
      }
      foreach ($value->getAll() as $param_key => $param_value) {
        $return[$param_key] = Accessor::convert($param_value, $themed);
      }
      return (object) $return;
    }
    elseif (is_array($value)) {
      // An array of potential values.
      $return = array();
      foreach ($value as $param_key => $param_value) {
        $return[$param_key] = Accessor::convert($param_value, $themed);
      }
      return $return;
    }
    else {
      // This is likely a scalar or stdClass.
      return $value;
    }
  }

  /**
   * Convert the value into a something suitable for JSON.
   */
  public function value() {
    return Accessor::convert($this->value, $this->themed);
  }
}
