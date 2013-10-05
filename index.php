<?php

/**
 * @file Demo index script for renderable.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Let's pretend we are Drupal, at least vaguely. :)
include('./fake-drupal/fake-drupal.php');

$app = new Silex\Application();

$app->get('/hello/{name}', function($name) use($app) {
    return 'Hello ' . $app->escape($name);
});

$app->get('/node/{id}', function($id) use($app) {
  $build = new RenderableBuilder('ThemeFullNode', array(
    'node' => node_load($id),
  ));
  return deliver($build);
});

$app->get('/itemList/{items}', function($items) use($app) {
  $build = new RenderableBuilder('ThemeItemList', array(
    'items' => explode(',', $items),
  ));
  return deliver($build);
});

$app->get('/something-fancy', function() use($app) {
  $build = new RenderableBuilder('ThemeItemList', array(
    'items' => array(
      new RenderableBuilder('ThemeFullNode', array(
        'node' => node_load(123),
      )),
      new RenderableBuilder('ThemeFullNode', array(
        'node' => node_load(456),
      )),
      new RenderableBuilder('ThemeFullNode', array(
        'node' => node_load(789),
      )),
      new RenderableBuilder('ThemeItemList', array(
        'items' => array(
          'red',
          'green',
          'blue',
        )
      )),
    ),
  ));
  return deliver($build);
});

$app->run();
