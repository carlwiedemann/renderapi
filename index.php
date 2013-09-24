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
  // Call any altering functions.
  // foreach (getAlterCallbacks() as $alterCallback) {
  //   // Alter callbacks receive the RenderableBuilder, can call methods, and
  //   // change build class.
  //   $alterCallback($build);
  // }

  // All altering has been done. So build the object, and subobjects.
  $renderable = $build->create();



  // 
  // // Now that the renderable exists, we need to decorate it based on other
  // // modules via decorator registry.
  // foreach (getModuleDecoratorClasses($renderable) as $moduleDecoratorClass) {
  //   $renderable = new $moduleDecoratorClass($renderable);
  // }
  // // Decorate with the theme.
  // if ($themeDecoratorClass = getThemeDecoratorClass($renderable)) {
  //   $renderable = new $themeDecoratorClass($renderable);
  // }

  // At this point, we are ready to render everything.
  return $renderable;
}

print deliver($build);


