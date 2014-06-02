<?php

namespace RenderAPI;

interface RenderableInterface extends WeightedInterface {

  public function render();

  public function __toString();

}
