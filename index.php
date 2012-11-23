<?php

include 'renderapi.inc';

$var = array(
  '#type' => 'table',
  'attributes' => array('id' => 'test', 'border' => '1'),
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

$table = RenderableFactory::create($var);

?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>I can haz render?</title>
  </head>
  <body>
    <table<?php r($table->attributes); ?>>
    <?php r($table->caption); ?>
      <?php r($table->colgroups); ?>
      <thead<?php r($table->header->attributes); ?>>
      <?php foreach ($table->header->inner as &$row): ?>
        <tr<?php r($row->attributes); ?>>
        <?php foreach ($row->inner as &$cell): ?>
          <td<?php r($cell->attributes); ?>><?php r($cell->inner); ?></td>
        <?php endforeach ?>
        </tr>
      <?php endforeach ?>
      </thead>
      <tbody<?php r($table->rows->attributes); ?>>
        <?php foreach ($table->rows->inner as &$row): ?>
          <tr<?php r($row->attributes); ?>>
            <?php foreach ($row->inner as &$cell): ?>
              <td<?php r($cell->attributes); ?>><?php r($cell->inner); ?></td>
            <?php endforeach ?>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </body>
</html>