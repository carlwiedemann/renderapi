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
class AustinFullNodeDecorator extends AbstractRenderableDecorator {

  function prepare() {
    parent::prepare();
    // Create a new variable.
    $this->set('subtitle', 'Here is a subtitle, yeehaw!');
  }

}
