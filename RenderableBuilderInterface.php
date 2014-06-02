<?php

namespace RenderAPI;

interface RenderableBuilderInterface extends WeightedInterface {

  public function setRenderable(RenderableInterface $renderable);

  public function renderableBuilt();

  public function getRenderable();

  public function buildRenderable();

  public function find($key);

  public function render();

  public function __toString();

}
