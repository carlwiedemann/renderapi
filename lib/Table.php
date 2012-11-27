<?php

// namespace RenderAPI\Core;

/**
 * Helper class for building tables. Turns a primary abstraction into a base
 * abstraction.
 */
class Table extends Renderable {
  protected $names = array(
    'caption',
    'colgroups',
    'header',
    'rows',
  );

  private $empty;

  public function __construct($arg) {

    // Consider whether we have empty rows.
    $this->empty = empty($arg['rows']);

    if ($this->empty) {
      // Build dummy row with empty text designator.
      $header_copy = $arg['header'];
      unset($header_copy['attributes']);
      $rows[] = array(
        '#type' => 'tr',
        'inner' => array(
          '#type' => 'td',
          'inner' => isset($arg['#empty']) ? $arg['#empty'] : '',
          'attributes' => array(
            'colspan' => (string) count($header_copy),
          ),
        ),
      );
    }
    else {
      // Build rows.
      $rows = array();
      foreach ($arg['rows'] as $row_key => $row_value) {
        if ($row_key !== 'attributes') {
          $row = array();
          foreach ($row_value as $cell_key => $cell_value) {
            if ($cell_key !== 'attributes') {
              if (isset($cell_value['#type']) && $cell_value['#type'] == 'td') {
                $row[$cell_key] = $cell_value;
              }
              else {
                $row[$cell_key] = array(
                  '#type' => 'td',
                  'inner' => $cell_value,
                  'attributes' => array(),
                );
              }
            }
          }
          $rows[$row_key] = array(
            '#type' => 'tr',
            'inner' => $row,
            'attributes' => isset($arg['rows'][$row_key]['attributes']) ? $arg['rows'][$row_key]['attributes']:array(),
          );
        }
      }
    }
    $arg['rows'] = array(
      '#type' => 'tbody',
      'inner' => $rows,
      'attributes' => isset($arg['rows']['attributes']) ? $arg['rows']['attributes']:array(),
    );

    // Build header.
    $row = array();
    foreach ($arg['header'] as $cell_key => $cell_value) {
      if ($cell_key !== 'attributes') {
        if (isset($cell_value['#type']) && $cell_value['#type'] == 'th') {
          $row[$cell_key] = $cell_value;
        }
        else {
          $row[$cell_key] = array(
            '#type' => 'th',
            'inner' => $cell_value,
            'attributes' => array(),
          );
        }
      }
    }
    $arg['header'] = array(
      '#type' => 'thead',
      'inner' => array(
        array(
          '#type' => 'tr',
          'inner' => $row,
          'attributes' => array(),
        )
      ),
      'attributes' => isset($arg['header']['attributes']) ? $arg['header']['attributes']:array(),
    );

    // Build caption.
    if (isset($arg['caption']) && isset($arg['caption']['#type']) && $arg['caption']['#type'] !== 'caption') {
      $arg['caption'] = array(
        '#type' => 'caption',
        'inner' => $arg['caption'],
        'attributes' => array(),
      );
    }

    // Build colgroups.
    if (isset($arg['colgroups'])) {
      $colgroups = array();
      foreach ($arg['colgroups'] as $colgroup_key => $colgroup_value) {
        $colgroup = array();
        foreach ($colgroup_value as $colgroup_item_key => $colgroup_item_value) {
          if ($colgroup_item_key === 'inner') {
            foreach ($colgroup_item_value as $col_key => $col_value) {
              if (isset($col_value['#type']) && $col_value['#type'] == 'col') {
                $col = $col_value;
              }
              elseif (isset($col_value['attributes'])) {
                $col = array(
                  '#type' => 'col',
                  'inner' => NULL,
                  'attributes' => $col_value['attributes'],
                );
              }
              if (!empty($col)) {
                $colgroup[$col_key] = $col;
              }
            }
          }
        }
        $colgroups[$colgroup_key] = array(
          '#type' => 'colgroup',
          'inner' => $colgroup,
          'attributes' => isset($arg['colgroups'][$colgroup_key]['attributes']) ? $arg['colgroups'][$colgroup_key]['attributes']:array(),
        );
      }
      // We'll just implode these.
      $arg['colgroups'] = $colgroups;
    }

    parent::__construct($arg);
  }
}