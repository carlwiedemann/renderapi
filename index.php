<?php

/**
 * @file Demo index script for renderable.
 */

$loader = require_once __DIR__ . '/vendor/autoload.php';

require_once './base.php';

use FakeDrupal\FakeDrupal;
use FakeDrupal\FakeDrupalRenderManager;
use FakeDrupal\FakeDrupalThemeEngine;
use RenderAPI\RenderAPI;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

$app['debug'] = TRUE;

FakeDrupal::setEnabledModules(array(
  'someexamples',
  // 'colorado', // Comment/uncomment this line to see effects.
));

FakeDrupal::setEnabledThemes(array(
  // 'austin', // Comment/uncomment this line to see effects.
));

FakeDrupal::bootstrap();

// Set render manager to use Drupal (will default to looking in ./templates).
RenderAPI::setRenderManager(new FakeDrupalRenderManager());

/**
 * Twig service provider.
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => RenderAPI::getRenderManager()->getWeightedTemplateDirectories(),
  'twig.options' => array(
    // 'cache' => __DIR__ . '/_tmp',
    'base_template_class' => 'FakeDrupal\AbstractFakeDrupalTwigTemplate',
    'autoescape' => FALSE,
    'auto_reload' => TRUE,
  ),
));

// Set theme engine to use twig (will default to PHPTemplate).
RenderAPI::setThemeEngine(new FakeDrupalThemeEngine($app['twig']));



/**
 * Simply a node.
 */
$app->get('/node/{id}', function($id, Request $request, Application $app) {


  $build = RenderAPI::create('ThemeNode', array(
             'node' => node_load($id),
           ));


  return delegate_response($build, $request, $app);
});



/**
 * Simply an ItemList.
 */
$app->get('/itemList', function(Request $request, Application $app) {


  $build = RenderAPI::create('ThemeItemList', array(
             'items' => array(
               'first',
               'second',
               'third',
             ),
           ));


  return delegate_response($build, $request, $app);
});



/**
 * A compound builder showing weights, similar to a view.
 */
$app->get('/something-fancy', function(Request $request, Application $app) {


  $build = RenderAPI::create(array(
             RenderAPI::create('ThemeNode', array(
               'node' => node_load(123),
             ), -1),
             RenderAPI::create('ThemeNode', array(
               'node' => node_load(456),
             ), 3),
             RenderAPI::create('ThemeNode', array(
               'node' => node_load(789),
             ), 0),
             RenderAPI::create('ThemeItemList', array(
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


  $build = RenderAPI::create('ThemePage', array(

             'head_title' => 'Hello',

             'content' => RenderAPI::create('ThemeNode', array(
               'node' => node_load(123),
             )),

             'sidebar_first' => RenderAPI::create(array(

               'nodes' => RenderAPI::create(array(
                 RenderAPI::create('ThemeNode', array(
                   'node' => node_load(456),
                 )),
                 RenderAPI::create('ThemeNode', array(
                   'node' => node_load(789),
                 )),
               )),

               'more_link' => '<a href="#">See more</a>',

             )),

             'sidebar_second' => 'Some other block',

             'header' => 'Welcome to my site!',

             'footer' => '<a href="http://github.com/c4rl/renderapi">http://github.com/c4rl/renderapi</a>',

           ));


  return delegate_response($build, $request, $app);
});



/**
 * Show some examples, complete with JSON equivalence.
 */
$app->get('/', function(Request $request, Application $app) {


  foreach (array(
      array(
        '/node/123',
        'Node via ThemeNode',
      ),
      array(
        '/itemList',
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
      $items[] = RenderAPI::create(array(
        '<strong>' . $callback[1] . '</strong>',
        RenderAPI::create('ThemeItemList', array(
        'items' => array(
          '<a href="' . $callback[0] . '">HTML</a>',
          '<a href="' . $callback[0] . '?path=.">As JSON</a>',
          '<a href="' . $callback[0] . '?path=.&themed=1">As JSON with template variables</a>',
        ))),
      ));
  }

  $build = RenderAPI::create('ThemeSomeExamples', array(
             'examples' => RenderAPI::create('ThemeItemList', array(
               'items' => $items,
             )),
           ));


  return delegate_response($build, $request, $app);
});



$app->run();
