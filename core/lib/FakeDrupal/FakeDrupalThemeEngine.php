<?php

namespace FakeDrupal;

use RenderAPI\RenderAPI;
use RenderAPI\RenderableInterface;
use RenderAPI\ThemeEngine;

class FakeDrupalThemeEngine extends ThemeEngine {

  const FILENAME_EXTENSION = '.html.twig';

  const DEBUG = TRUE;

  public function __construct($proxyEngine) {
    if (!$proxyEngine instanceOf \Twig_Environment) {
      throw new \Exception('Please provide Twig_Environment');
    }
    $this->proxyEngine = $proxyEngine;
  }

  /**
   * We'll use our twig configuration.
   */
  public function render(RenderableInterface $renderable) {
    $tag = self::DEBUG ? '<!-- ' . RenderAPI::getRenderManager()->getTemplateDirectory($renderable) . '/' . $renderable->getTemplateName() . self::FILENAME_EXTENSION . '-->'  . PHP_EOL . PHP_EOL : '';
    return $tag . $this->proxyEngine->render($renderable->getTemplateName() . self::FILENAME_EXTENSION, $renderable->getAll());
  }

}
