<?php

/**
 * @file Demo index script for renderable.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Let's pretend we are Drupal, at least vaguely. :)
include('./fake-drupal/fake-drupal.php');

$app = new Silex\Application();

$app['debug'] = TRUE;

$app->get('/', function() use($app) {
  $build = new RenderableBuilder('ThemeSomeExamples', array(
      'examples' => new RenderableBuilder('ThemeItemList', array(
        'items' => array(
          '<a href="/node/123">Node via ThemeFullNode</a>',
          '<a href="/itemList/first,second,third">Item list via ThemeItemList</a>',
          '<a href="/something-fancy">Compound builder</a>',
          '<a href="/built-page">Sample page template</a>',
        ),
      )),
    ));
  return render($build);
});

$app->get('/node/{id}', function($id) use($app) {
  $build = new RenderableBuilder('ThemeFullNode', array(
    'node' => node_load($id),
  ));
  return render($build);
});

$app->get('/itemList/{items}', function($items) use($app) {
  $build = new RenderableBuilder('ThemeItemList', array(
    'items' => explode(',', $items),
  ));
  return render($build);
});

$app->get('/something-fancy', function() use($app) {
  $build = array(
      new RenderableBuilder('ThemeFullNode', array(
        'node' => node_load(123),
      ), -1),
      new RenderableBuilder('ThemeFullNode', array(
        'node' => node_load(456),
      ), 3),
      new RenderableBuilder('ThemeFullNode', array(
        'node' => node_load(789),
      ), 0),
      new RenderableBuilder('ThemeItemList', array(
        'items' => array(
          'red',
          'green',
          'blue',
        ),
      ), -1),
    );
  return render($build);
});

$app->get('/built-page', function() use($app) {
  $build = new RenderableBuilder('ThemePage', array(
    'head_title' => 'Hello',
    'content' => new RenderableBuilder('ThemeFullNode', array(
      'node' => node_load(123),
    )),
    'sidebar_first' => array(
      'Some block',
      new RenderableBuilder('ThemeItemList', array(
        'items' => array(
          'first',
          'second',
          new RenderableBuilder('ThemeFullNode', array(
            'node' => node_load(456),
          )),
        ),
      )),
    ),
    'sidebar_second' => 'Some other block',
    'header' => 'Some header',
    'footer' => 'Some footer',
  ));
  return render($build);
});

$app->run();
