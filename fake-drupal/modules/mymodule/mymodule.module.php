<?php

/**
 * @file A fake custom module.
 */

/**
 * An example of an alter callback.
 */
function mymodule_alter_node_view($build) {
  $build->setParam('foo', 'bar');
  $build->setBuildClass('ThemeFoo');
}
