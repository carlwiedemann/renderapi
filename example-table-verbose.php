<?php

// Unabstracted render array. Can be sent as argument to new Renderable($arg);
$foo = array(
  '#type' => 'table',
  '#empty' => 'My empty text',
  'attributes' => array(
    'class' => array('admin', 'stats'),
    'data-foo' => 'bar',
  ),
  'caption' => array(
    '#type' => 'caption',
    'attributes' => array(),
    'inner' => 'My caption',
  ),
  'colgroups' => array(
    '#type' => 'colgroup',
    'inner' => array(
      array(
        '#type' => 'col',
        'inner' => NULL,
        'attributes' => array('class' => 'first'),
      ),
      array(
        '#type' => 'col',
        'inner' => NULL,
        'attributes' => array('class' => 'second'),
      ),
    ),
    'attributes' => array('class' => 'two'),
  ),
  'header' => array(
    '#type' => 'tbody',
    'attributes' => array(),
    'inner' => array(
      '#type' => 'tr',
      'attributes' => array(),
      'inner' => array(
        array(
          '#type' => 'th',
          'attributes' => array(),
          'inner' => 'First',
        ),
        array(
          '#type' => 'th',
          'attributes' => array(),
          'inner' => 'Second',
        ),
        array(
          '#type' => 'th',
          'attributes' => array(),
          'inner' => 'Third',
        ),
      ),
    ),
  ),
  'rows' => array(
    '#type' => 'tbody',
    'attributes' => array(),
    'inner' => array(
      array(
        '#type' => 'tr',
        'attributes' => array(),
        'inner' => array(
          array(
            '#type' => 'td',
            'attributes' => array(),
            'inner' => '1',
          ),
          array(
            '#type' => 'td',
            'attributes' => array(),
            'inner' => '2',
          ),
          array(
            '#type' => 'td',
            'attributes' => array(),
            'inner' => '3',
          ),
        ),
      ),
      array(
        '#type' => 'tr',
        'attributes' => array(),
        'inner' => array(
          array(
            '#type' => 'td',
            'attributes' => array(),
            'inner' => '4',
          ),
          array(
            '#type' => 'td',
            'attributes' => array(),
            'inner' => '5',
          ),
          array(
            '#type' => 'td',
            'attributes' => array(),
            'inner' => '6',
          ),
        ),
      ),
    ),
  ),
);
