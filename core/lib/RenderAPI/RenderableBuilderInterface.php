<?php

namespace RenderAPI;

interface RenderableBuilderInterface extends WeightedInterface {

  public function find($key);

  public function render();

  public function __toString();

}
