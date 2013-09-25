<?php

/**
 * @file Demo index script for renderable.
 */

// Let's pretend we are Drupal, at least vaguely. :)
include('./fake-drupal/fake-drupal.php');

// Get some build, as if it came from a menu callback.
$build = new RenderableBuilder('ThemeFullNode', array(
  'node' => node_load(123),
));

print deliver($build);
