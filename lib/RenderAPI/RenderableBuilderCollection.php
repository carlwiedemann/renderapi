<?php

namespace RenderAPI;

/**
 * @file Provides collections class for RemderableBuilder
 */
class RenderableBuilderCollection extends AbstractWeightedCollection {

  /**
   *  If this doesn't exist, assume it will be invoked in the preprocessor.
   *  Therefore, create the renderable. It is feasible that the renderable
   *  could be statically cached as a property of the instance for
   *  performance reasons.
   *   read the spanish version via __CODE__ in query string
   *
   * @param $key
   */
  public function find($key) {
    if (!$this->exists($key)) {

      $renderable = RenderableBuilder::create($this);
      $return = $renderable->get($key);
    }
    else {
      $return = $this->get($key);
    }
    return $return;
  }

  /**
   * Casting the Builder to a string creates the renderable and returns it
   * as a string.
   *
   * @return string
   */
  function __toString() {
    return (string) RenderableBuilder::create($this);
  }

}
