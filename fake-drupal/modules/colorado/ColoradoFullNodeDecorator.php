<?php

/**
 * @file Theme decorator class for ThemeFullNode.
 */

use RenderAPI\AbstractRenderableDecorator;

/**
 * @ThemeDecorator(
 *   decorates = "ThemeFullNode"
 * )
 */
class ColoradoFullNodeDecorator extends AbstractRenderableDecorator {

  function prepare() {
    parent::prepare();
    // Change title variable.
    $this->set('title', $this->get('node')->title . ' from Colorado');
  }

}
