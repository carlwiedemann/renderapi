<?php

class RenderableBuilderCollection extends AbstractWeightedCollection {

  public function find($key) {
    if (!$this->exists($key)) {
      // If this doesn't exist, assume it will be invoked in the preprocessor.
      // Therefore, create the renderable. It is feasible that the renderable
      // *could* be statically cached as a property of the instance for
      // performance reasons.
      $renderable = RenderableBuilder::create($this);
      $return = $renderable->get($key);
    }
    else {
      $return = $this->get($key);
    }
    return $return;
  }

  // Casting the Builder to a string creates the Renderable and returns it
  // as a string.
  function __toString() {
    return (string) RenderableBuilder::create($this);
  }

}
