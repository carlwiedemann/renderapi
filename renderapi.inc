<?php

function check_plain($arg) {
  // This is just a dummy function for now.
  return $arg;
}

function r($renderable) {
  $renderable->show();
  print (string) $renderable;
}

/**
 * @todo Learn how to load namespaces properly. :)
 */
include './lib/AttributeValueBase.php';
include './lib/Attribute.php';
include './lib/AttributeArray.php';
include './lib/AttributeBoolean.php';
include './lib/AttributeString.php';
include './lib/RenderableBase.php';
include './lib/Renderable.php';
include './lib/RenderableElement.php';
include './lib/RenderableCollection.php';
include './lib/RenderableScalar.php';
include './lib/RenderableFactory.php';
include './lib/Table.php';
