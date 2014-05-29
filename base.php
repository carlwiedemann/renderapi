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
    // Set application to leverage proper theme engine.
    RenderAPI::setThemeEngine($app['twig']);
    // Return rendered HTML.
    return $build->render();
  }
}

/**
 * Shortcut.
 */
function r() {
  return call_user_func_array('RenderAPI\RenderAPI::create', func_get_args());
}
