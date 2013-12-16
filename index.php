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
    $params = array_filter(explode('.', $request->query->get('path')), function($a) { return $a !== ''; });
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
 * Shortcut for factory for DX purposes.
 *
 * @param mixed
 *   The first parameter could be either the classname (if creating a
 *   RenderableBuilder object) or an array of RenderableBuilder objects (if creating
 *   a RenderableBuilderCollection object).
 *
 * @param mixed
 *   The second parameter could either be the array of arguments (if creating a
 *   RenderableBuilder object) or the weight (if creating a
 *   RenderableBuilderCollection object).
 *
 * @param mixed
 *   The third parameter is the weight if creating a RenderableBuilder object.
 *
 * @return Will either return a RenderableBuilder or a
 * RenderableBuilderCollection depending on arguments.
 */
function r() {
  $args = func_get_args();
  // If the first argument is a string, this follows what we'd expect for
  // a RenderableBuilder.
  if (is_string($args[0])) {
    $class = $args[0];
    $params = isset($args[1]) ? $args[1] : NULL;
    $weight = isset($args[2]) ? $args[2] : NULL;
    return new RenderableBuilder($class, $params, $weight);
  }
  // If the first argument is an array, this follows what we'd expect for
  // a RenderableBuilderCollection.
  elseif (is_array($args[0])) {
    $params = $args[0];
    $weight = isset($args[1]) ? $args[1] : NULL;
    return new RenderableBuilderCollection($params, $weight);
  }
  else {
    return NULL;
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
