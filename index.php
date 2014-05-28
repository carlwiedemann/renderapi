<?php

/**
 * @file Demo index script for renderable.
 */

$loader = require_once __DIR__ . '/vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use RenderAPI\Accessor;
use RenderAPI\RenderAPI;

// Let's pretend we are Drupal, at least vaguely. :)
include './fake-drupal/fake-drupal.php';
include './base.php';

$app = new Silex\Application();

$app['debug'] = TRUE;

/**
 * Show some examples, complete with JSON equivalence.
 */
$app->get('/', function(Request $request, Application $app) {

  foreach (array(
      array(
        '/node/123',
        'Node via ThemeFullNode',
      ),
      array(
        '/itemList/first,second,third',
        'Item list via ThemeItemList'
      ),
      array(
        '/something-fancy',
        'Compound builder',
      ),
      array(
        '/built-page',
        'Sample page template',
      ),
    ) as $callback) {
      $items[] = r(array(
        '<strong>' . $callback[1] . '</strong>',
        r('ThemeItemList', array(
        'items' => array(
          '<a href="' . $callback[0] . '">HTML</a>',
          '<a href="' . $callback[0] . '?path=.">As JSON</a>',
          '<a href="' . $callback[0] . '?path=.&themed=1">As JSON with template variables</a>',
        ))),
      ));
  }

  $build = r('ThemeSomeExamples', array(
      'examples' => r('ThemeItemList', array(
        'items' => $items,
      )),
    ));

  return delegate_response($build, $request, $app);
});

/**
 * Simply a node.
 */
$app->get('/node/{id}', function($id, Request $request, Application $app) {

  $build = r('ThemeFullNode', array(
    'node' => node_load($id),
  ));

  return delegate_response($build, $request, $app);
});

/**
 * Simply an ItemList.
 */
$app->get('/itemList/{items}', function($items, Request $request, Application $app) {

  $build = r('ThemeItemList', array(
    'items' => explode(',', $items),
  ));

  return delegate_response($build, $request, $app);
});

/**
 * A compound builder showing weights, similar to a view.
 */
$app->get('/something-fancy', function(Request $request, Application $app) {

  $build = r(array(
      r('ThemeFullNode', array(
        'node' => node_load(123),
      ), -1),
      r('ThemeFullNode', array(
        'node' => node_load(456),
      ), 3),
      r('ThemeFullNode', array(
        'node' => node_load(789),
      ), 0),
      r('ThemeItemList', array(
        'items' => array(
          'red',
          'green',
          'blue',
        ),
      ), -1),
    ));

  return delegate_response($build, $request, $app);
});

/**
 * A more involved page.
 */
$app->get('/built-page', function(Request $request, Application $app) {

  $build = r('ThemePage', array(
    'head_title' => 'Hello',
    'content' => r('ThemeFullNode', array(
      'node' => node_load(123),
    )),
    'sidebar_first' => r(array(
      'Some block',
      r('ThemeItemList', array(
        'items' => array(
          'first',
          'second',
          r('ThemeFullNode', array(
            'node' => node_load(456),
          )),
        ),
      )),
    )),
    'sidebar_second' => 'Some other block',
    'header' => 'Some header',
    'footer' => 'Some footer',
  ));

  return delegate_response($build, $request, $app);
});

$app->run();
