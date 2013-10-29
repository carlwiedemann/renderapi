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
    $this->themed = (bool) $themed;
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
  public function get($key) {

    // Return null for scalars and anything else.
    $return = NULL;

    if ($this->value instanceOf RenderableCollection || $this->value instanceOf RenderableBuilderCollection) {
      // If this is a component of an array, recurse.
      $return = Accessor::create($this->value->get($key), $this->themed);
    }
    elseif ($this->value instanceOf AbstractRenderable || $this->value instanceOf RenderableBuilder) {
      // If this is a AbstractRenderable or a RenderableBuilder, call the get() method.
      if ($this->themed && $this->value instanceOf RenderableBuilder) {
        $value = RenderableBuilder::create($this->value);
      }
      else {
        $value = $this->value;
      }
      $return = Accessor::create($value->get($key), $this->themed);
    }
    elseif (is_object($this->value) && isset($this->value->$key)) {
      // If this is a struct, treat as much.
      $return = Accessor::create($this->value->$key, $this->themed);
    }
    elseif (is_array($this->value) && isset($this->value[$key])) {
      $return = Accessor::create($this->value[$key], $this->themed);
    }
    return $return;
  }

  /**
   * Recursively converts a given variable into a something to be sent to a JSON
   * response.
   */
  static public function convert($variable, $themed) {
    if ($variable instanceOf AbstractRenderable || $variable instanceOf RenderableBuilder) {
      $return = array();
      // Builders get converted into Renderables.
      if ($themed && $variable instanceOf RenderableBuilder) {
        $variable = RenderableBuilder::create($variable);
        $variable->prepare();
      }
      foreach ($variable->getAll() as $key => $value) {
        $return[$key] = Accessor::convert($value, $themed);
      }
      return (object) $return;
    }
    elseif ($variable instanceOf RenderableBuilderCollection) {
      // An array of potential values.
      $return = array();
      foreach ($variable->getAllByWeight() as $key => $value) {
        $return[$key] = Accessor::convert($value, $themed);
      }
      return $return;
    }
    elseif ($variable instanceOf RenderableCollection) {
      // An array of potential values.
      $return = array();
      foreach ($variable->getAll() as $key => $value) {
        $return[$key] = Accessor::convert($value, $themed);
      }
      return $return;
    }
    elseif (is_array($variable)) {
      // An array of potential values.
      $return = array();
      foreach ($variable as $key => $value) {
        $return[$key] = Accessor::convert($value, $themed);
      }
      return $return;
    }
    else {
      // This is likely a scalar or stdClass.
      return $variable;
    }
  }

  /**
   * Convert the value into a something suitable for JSON.
   */
  public function value() {
    return Accessor::convert($this->value, $this->themed);
  }
}
