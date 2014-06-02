<?php

namespace RenderAPI;

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

  /**
   * The given value to be accessed.
   *
   * @var mixed
   */
  private $value;

  /**
   * Whether the converted state of a RenderableBuilder will be delegated to
   * the base class.
   *
   * @var boolean
   */
  private $themed;

  /**
   * Constructor receives value and whether the output should be themed or not.
   *
   * @param mixed $value
   * @param boolean $themed
   */
  function __construct($value, $themed = FALSE) {
    $this->value = $value;
    $this->themed = (bool) $themed;
  }

  /**
   * Factory class method.
   *
   * @param mixed $value
   * @param boolean $themed
   * @return Accessor
   */
  public static function create($value, $themed = FALSE) {
    return new Accessor($value, $themed);
  }

  /**
   * Provides a given internal variable in a format suitable for JSON conversion.
   *
   * @param string $key
   * @return mixed
   */
  public function get($key) {

    if ($this->value instanceOf AbstractWeightedCollection) {
      // If this is a component of an array, recurse.
      $return = Accessor::create($this->value->get($key), $this->themed);
    }
    elseif (is_object($this->value) && isset($this->value->$key)) {
      // If this is a struct, treat as much.
      $return = Accessor::create($this->value->$key, $this->themed);
    }
    elseif (is_array($this->value) && isset($this->value[$key])) {
      $return = Accessor::create($this->value[$key], $this->themed);
    }
    else {
      // This didn't find anything, so it must be invalid key.
      $return = Accessor::create(NULL);
    }

    return $return;
  }

  /**
   * Recursively converts a given variable into a something to be sent to a JSON
   * response.
   *
   * @param mixed $variable
   * @return mixed
   */
  public static function convert($variable, $themed) {
    if ($variable instanceOf RenderableBuilderInterface || $variable instanceOf RenderableInterface) {
      $return = array();
      // For themed output, build renderable.
      if ($themed && $variable instanceOf RenderableBuilderInterface) {
        $variable = $variable->getRenderable();
      }
      foreach ($variable->getAll() as $key => $value) {
        $return[$key] = Accessor::convert($value, $themed);
      }
    }
    elseif (is_array($variable)) {
      // An array of potential values.
      $return = array();
      foreach ($variable as $key => $value) {
        $return[$key] = Accessor::convert($value, $themed);
      }
    }
    else {
      // This is likely a scalar or stdClass.
      $return = $variable;
    }
    return $return;
  }

  /**
   * Convert the value into a something suitable for JSON.
   *
   * @return mixed
   */
  public function value() {
    return Accessor::convert($this->value, $this->themed);
  }

}
