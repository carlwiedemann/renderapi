<?php

namespace RenderAPI;

use RenderAPI\RenderableBuilder;
use RenderAPI\RenderableBuilderCollection;

class RenderAPI {

  /**
   * Render factory method.
   *
   * @param mixed
   *   The first parameter could be either the classname (if creating a
   *   RenderableBuilder object) or an array of RenderableBuilder objects (if creating
   *   a RenderableBuilderCollection object).
   *
   * @param mixed
   *   The second parameter could either be the array of arguments (if creating a
   *   RenderableBuilder object) or the weight (if creating a
   *   RenderableBuilderCollection object).
   *
   * @param mixed
   *   The third parameter is the weight if creating a RenderableBuilder object.
   *
   * @return Will either return a RenderableBuilder or a
   * RenderableBuilderCollection depending on arguments.
   */
  static function create() {
    $args = func_get_args();
    // If the first argument is a string, this follows what we'd expect for
    // a RenderableBuilder.
    if (is_string($args[0])) {
      $class = $args[0];
      $params = isset($args[1]) ? $args[1] : array();
      $weight = isset($args[2]) ? $args[2] : 0;
      return new RenderableBuilder($class, $params, $weight);
    }
    // If the first argument is an array, this follows what we'd expect for
    // a RenderableBuilderCollection.
    elseif (is_array($args[0])) {
      $params = $args[0];
      $weight = isset($args[1]) ? $args[1] : 0;
      return new RenderableBuilderCollection($params, $weight);
    }
    else {
      return NULL;
    }
  }

}
