<?php

namespace RenderAPI;

interface RenderableInterface extends WeightedInterface {

  public function prepare();

  public function render();

  public function __toString();

}
