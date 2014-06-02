<?php

namespace RenderAPI;

trait RenderableBuilderTrait {

  /**
   * The renderable this builder will generate. Once generated, this is stored
   * as a parameter.
   *
   * @var AbstractRenderable
   */
  protected $renderable;

  public function setRenderable(RenderableInterface $renderable) {
    $this->renderable = $renderable;
  }

  public function renderableBuilt() {
    return isset($this->renderable);
  }

  public function getRenderable() {
    return $this->renderable;
  }

  /**
   * Suppose we have a separate method similar to get() that is used
   * exclusively via the them layer. Consider whether there should exist a
   * global constraint.
   *
   * @param string $key
   * @return mixed
   */
  public function find($key) {
    $return = NULL;

    if ($this->exists($key)) {
      $return = $this->get($key);
    }
    else {
      if (!isset($this->renderable)) {
        // If this doesn't exist, assume it will be invoked in the preprocessor.
        // Therefore, create the renderable. It is feasible that the renderable
        // *could* be statically cached as a property of the instance for
        // performance reasons.
        $this->renderable = RenderAPI::createRenderable($this);
      }
      $return = $this->renderable->get($key);
    }

    return $return;
  }

  /**
   * @return string
   */
  public function render() {
    $renderable = RenderAPI::createRenderable($this);
    return $renderable->render();
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->render();
  }

}
