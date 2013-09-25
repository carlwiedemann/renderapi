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
