<?php

namespace RenderAPI;

trait RenderableBuilderTrait {

  /**
   * The renderable this builder will generate. Once generated, this is stored
   * as a parameter.
   *
   * @var AbstractRenderable
   */
  private $renderable;

  public function setRenderable(RenderableInterface $renderable) {
    $this->renderable = $renderable;
  }

  public function renderableBuilt() {
    return isset($this->renderable);
  }

  public function getRenderable() {
    if (!isset($this->renderable)) {
      $this->buildRenderable();
    }
    return $this->renderable;
  }

  public function buildRenderable() {
    $this->renderable = RenderAPI::createRenderable($this);
  }

  /**
   * Looks for $key in parameters, then the parameters of the renderable.
   * Used for drillability purposes.
   *
   * @param string $key
   * @return mixed
   */
  public function find($key) {
    if ($this->exists($key)) {
      return $this->get($key);
    }
    else {
      // If this doesn't exist, assume it will be invoked in the preprocessor.
      return $this->getRenderable()->get($key);
    }
  }

  /**
   * @return string
   */
  public function render() {
    return $this->getRenderable()->render();
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->render();
  }

}
