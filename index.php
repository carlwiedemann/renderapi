<?php

/**
 * @file Let's pretend we are Drupal, at least very, very vaguely. :)
 */

include('./fake-drupal/fake-drupal.php');

// Get some build, as if it came from a menu callback.
$build = new RenderableBuilder('ThemeFullNode', array(
  'node' => node_load(123),
));

function deliver($build) {

  // @todo Weight

  // Concatenate components.
  if (is_array($build)) {
    $markup = '';
    foreach ($build as $sub_build) {
      $markup .= (string) $sub_build->create();
    }
  }
  else {
    $markup = (string) $build->create();
  }

  // At this point, we are ready to render everything.
  return $markup;
}

print deliver($build);
