<?php

namespace RenderAPI;

interface RenderManagerInterface {

  public function alter(RenderableBuilderInterface $builder);

  public function decorate(RenderableInterface $renderable);

  public function templateExists(RenderableInterface $renderable);

}
