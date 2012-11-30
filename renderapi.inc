<?php

/**
 * @file This is just some glue code to get the proof of concept working.
 */


function check_plain($arg) {
  // This is just a dummy function for now.
  return $arg;
}

/**
 * Render and print a string.
 */
function r($renderable) {
  if (method_exists($renderable, 'show')) {
    $renderable->show();
  }
  print (string) $renderable;
}

define('WEBROOT', getcwd());

/**
 * @todo Learn how to load namespaces properly. :)
 */
include WEBROOT . '/lib/AttributeValueBase.php';
include WEBROOT . '/lib/Attribute.php';
include WEBROOT . '/lib/AttributeArray.php';
include WEBROOT . '/lib/AttributeBoolean.php';
include WEBROOT . '/lib/AttributeString.php';
include WEBROOT . '/lib/RenderableBase.php';
include WEBROOT . '/lib/Renderable.php';
include WEBROOT . '/lib/RenderableCollection.php';
include WEBROOT . '/lib/RenderableScalar.php';
include WEBROOT . '/lib/RenderableFactory.php';
include WEBROOT . '/lib/Table.php';

/**
 * Let's make a dummy phptemplate engine.
 */
define('ENGINE', 'phptemplate');

if (defined('ENGINE')) {
  define('ENGINE_DIR', WEBROOT . '/engines/' . ENGINE);
  include ENGINE_DIR . '/' . ENGINE . '.engine';
}
