<?php

namespace RenderAPI;

interface ThemeEngineInterface {

  public function __construct($proxyEngine);

  public function render(RenderableInterface $renderable);

}
