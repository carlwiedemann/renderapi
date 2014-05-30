<?php

namespace RenderAPI;

/**
 * Dummy class.
 */
class RenderManager implements RenderManagerInterface {

  public function alter(RenderableBuilderInterface $builder) {
  }

  public function decorate(RenderableInterface $renderable) {
    return $renderable;
  }

  public function baseTemplateExists(RenderableInterface $renderable) {
    $themeEngine = RenderAPI::getThemeEngine();
    return file_exists('./templates/' . $renderable->getTemplateName() . $themeEngine::FILENAME_EXTENSION);
  }

  public function getTemplateDirectory(RenderableInterface $renderable) {
    return './templates';
  }

  public function getWeightedTemplateDirectories() {
    return array('./templates');
  }

}
