<?php

/**
 * @file A proof of concept.
 */

// Include what we need.
include 'renderapi.inc';



$var = array(
  '#type' => 'table',
  'attributes' => array('id' => 'test', 'border' => '1', 'class' => array('my-table')),
  'header' => array('One', 'Two', 'Three'),
  'caption' => 'My first Table',
  'colgroups' => array(
    array(
      'attributes' => array('style' => 'background-color: green'),
      'inner' => array(
        array('attributes' => array()),
        array('attributes' => array('style' => 'background-color: yellow')),
        array('attributes' => array()),
      ),
    ),
  ),
  'rows' => array(
    array(
      array(
        '#type' => 'link',
        'attributes' => array('href' => 'http://www.google.com'),
        'inner' => 'Google',
        ),
      2,
      3),
    array(4,
      array(
        '#type' => 'td',
        'attributes' => array(
          'colspan' => '2',
          'align' => 'center',
        ),
        'inner' => 5,
      ),
    ),
  ),
);

// Renderables could be alterable based on the menu callback.
foo_PAGE_CALLBACK_render_alter($var);
function foo_PAGE_CALLBACK_render_alter(&$var) {
  // Add ID to caption.
  $var['caption'] = array(
    '#type' => 'caption',
    'attributes' => array('id' => 'the-caption'),
    'inner' => $var['caption'],
  );
}

// Create the renderable to be sent to the page template.
$content = RenderableFactory::create($var);

?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>I can haz render?</title>
  </head>
  <body>
    <?php r($content); ?>
  </body>
</html>