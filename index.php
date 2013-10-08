<?php

/**
 * @file Demo index script for renderable.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

// Let's pretend we are Drupal, at least vaguely. :)
include('./fake-drupal/fake-drupal.php');

$app = new Silex\Application();

$app['debug'] = TRUE;

/**
 * Provides a Silex response per rendered HTML, or an JSON response.
 */
function delegate_response($build, Request $request, Application $app) {
  // If we have ?path set, we'll return as JSON.
  if ($request->query->get('path')) {
    // If we have ?themed set, we'll delegate to Renderables.
    $accessor = Accessor::create($build, $request->query->get('themed'));
    // Parse path to send to accessor.
    $params = array_filter(explode('.', $request->query->get('path')), function($a) { return isset($a); });
    // Churn through accessor.
    foreach ($params as $param) {
      $accessor = $accessor->get($param);
    }
    // Return value as JSON.
    return $app->json($accessor->value());
  }
  else {
    // Return rendered HTML.
    return render($build);
  }
}

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
      $items[] = array(
        '<strong>' . $callback[1] . '</strong>',
        new RenderableBuilder('ThemeItemList', array(
        'items' => array(
          '<a href="' . $callback[0] . '">HTML</a>',
          '<a href="' . $callback[0] . '?path=.">As JSON</a>',
          '<a href="' . $callback[0] . '?path=.&themed=1">As JSON with template variables</a>',
        ))),
      );
  }

  $build = new RenderableBuilder('ThemeSomeExamples', array(
      'examples' => new RenderableBuilder('ThemeItemList', array(
        'items' => $items,
      )),
    ));

  return delegate_response($build, $request, $app);
});

/**
 * Simply a node.
 */
$app->get('/node/{id}', function($id, Request $request, Application $app) {
  $build = new RenderableBuilder('ThemeFullNode', array(
    'node' => node_load($id),
  ));
  return delegate_response($build, $request, $app);
});

/**
 * Simply an ItemList.
 */
$app->get('/itemList/{items}', function($items, Request $request, Application $app) {
  $build = new RenderableBuilder('ThemeItemList', array(
    'items' => explode(',', $items),
  ));
  return delegate_response($build, $request, $app);
});

/**
 * A compound builder showing weights, similar to a view.
 */
$app->get('/something-fancy', function(Request $request, Application $app) {
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
  return delegate_response($build, $request, $app);
});

/**
 * A more involved page.
 */
$app->get('/built-page', function(Request $request, Application $app) {
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
  return delegate_response($build, $request, $app);
});

$app->run();
