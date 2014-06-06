<?php

namespace RenderAPI;

interface RenderManagerInterface {

  public function alter(RenderableBuilderInterface $builder);

  public function decorate(RenderableInterface $renderable);

  public function baseTemplateExists(RenderableInterface $renderable);

  public function getTemplateDirectory(RenderableInterface $renderable);

  public function getWeightedTemplateDirectories();

}
