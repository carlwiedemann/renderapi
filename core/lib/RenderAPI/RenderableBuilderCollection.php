<?php

namespace RenderAPI;

/**
 * @file Provides collections class for RemderableBuilder
 */
class RenderableBuilderCollection extends AbstractWeightedCollection implements RenderableBuilderInterface {

  /**
   *  If this doesn't exist, assume it will be invoked in the preprocessor.
   *  Therefore, create the renderable. It is feasible that the renderable
   *  could be statically cached as a property of the instance for
   *  performance reasons.
   *
   * @param $key string
   * @return mixed
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
   * @return string
   */
  public function render() {
    return (string) RenderableBuilder::create($this);
  }

  /**
   * Casting the Builder to a string creates the renderable and returns it
   * as a string.
   *
   * @return string
   */
  function __toString() {
    return $this->render();
  }

}
