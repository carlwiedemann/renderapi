<?php

/**
 * @file A fake custom module.
 */

use RenderAPI\RenderableBuilder;
use RenderAPI\RenderableBuilderInterface;

/**
 * An example of an alter callback.
 */
function mymodule_alter_node_view(RenderableBuilderInterface $build) {
  // $build->setBuildClass('ThemeFoo');
  // $build->set('foo', 'bar');
}

/**
 * Some other alter callback.
 */
function mymodule_alter_item_list(RenderableBuilderInterface $build) {
  // $items = $build->get('items');
  // $items[] = new RenderableBuilder('ThemeFullNode', array(
  //   'node' => node_load(789),
  // ));
  // $build->set('items', $items);
}
