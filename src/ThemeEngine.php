<?php

namespace RenderAPI;

class ThemeEngine implements ThemeEngineInterface {

  const FILENAME_EXTENSION = '.tpl.php';

  const DEBUG = TRUE;

  private $proxyEngine;

  public function __construct($proxyEngine = NULL) {
    $this->proxyEngine = $proxyEngine;
  }

  public function render(RenderableInterface $renderable) {
    if (isset($this->proxyEngine)) {
      return $this->proxyEngine->render($renderable);
    }
    else {
      // Default to PHPTemplate for now.
      extract($renderable->getAll(), EXTR_SKIP);
      // Start output buffering.
      ob_start();
      // Include the template file.
      $fullPath = RenderAPI::getRenderManager()->getTemplateDirectory($renderable) . '/' . $renderable->getTemplateName() . self::FILENAME_EXTENSION;
      if (self::DEBUG) {
        // Provide indicator of the file at the top.
        echo '<!-- Template: ' . $fullPath . ' -->' . PHP_EOL . PHP_EOL;
      }
      include $fullPath;
      // End buffering and return its
      return ob_get_clean();
    }
  }

}
