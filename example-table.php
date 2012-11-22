<?php

// Abstracted render array. Sent as argument to new Table($arg);
$foo = array(
  '#type' => 'table',
  '#empty' => 'My empty text',
  'attributes' => array(
    'class' => array('admin', 'stats'),
    'data-foo' => 'bar',
  ),
  'caption' => 'My caption',
  'colgroups' => array(
    'attributes' => array(
      'class' => array('two')
    ),
    array(
      'attributes' => array('class' => array('first')),
    ),
    array(
      'attributes' => array('class' => array('second')),
    ),
  ),
  'header' => array(
    'First', 'Second', 'Third',
  ),
  'rows' => array(
    array(1, 2, 3),
    array(4, 5, 6),
  ),
);
