<?php

/**
 * @file A fake custom module.
 */

/**
 * An example of an alter callback.
 */
function mymodule_alter_node_view($build) {
  // Set an arbitrary variable as part of the renderable structure.
  $build->set('foo', 'bar');
  // Change the finally built class.
  $build->setBuildClass('ThemeFoo');
}


/**
 * Some other alter callback.
 */
function mymodule_alter_item_list($build) {
  $items = $build->get('items');
  $items[] = new RenderableBuilder('ThemeFullNode', array(
    'node' => node_load(789),
  ));
  $build->set('items', $items);
}
