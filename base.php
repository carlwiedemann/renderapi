<?php

/**
 * @file Functions for the sake of our Silex example.
 */

use RenderAPI\Accessor;
use RenderAPI\RenderAPI;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a Silex response per rendered HTML, or an JSON response.
 */
function delegate_response($build, Request $request, Application $app) {
  // If we have ?rendervar set, we'll return as JSON.
  if ($request->query->get('rendervar')) {
    // If we have ?prepare set, we'll delegate to Renderables.
    $accessor = Accessor::create($build, $request->query->get('prepare'));
    // Parse drill to send to accessor.
    $params = array_filter(explode('.', $request->query->get('rendervar')), function($a) { return $a !== ''; });
    // Churn through accessor.
    foreach ($params as $param) {
      $accessor = $accessor->get($param);
    }
    // Return value as JSON.
    return $app->json($accessor->value());
  }
  else {
    // Return rendered HTML.
    return $build->render();
  }
}

/**
 * Shortcut.
 */
// function r() {
//   return call_user_func_array('RenderAPI\RenderAPI::create', func_get_args());
// }

function _log($var) {
  file_put_contents('/tmp/php.log', var_export($var, 1) . PHP_EOL, FILE_APPEND);
}
