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
        'inner' => array(
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
              $row[$cell_key] = array(
                'inner' => $this->parseInner($cell_value),
                'attributes' => $this->parseAttributes($cell_value),
              );
            }
          }
          $rows[$row_key] = array(
            'inner' => $row,
            'attributes' => $this->parseAttributes($arg['rows'][$row_key]),
          );
        }
      }
    }
    $arg['rows'] = array(
      'inner' => $rows,
      'attributes' => $this->parseAttributes($arg['rows']),
    );

    // Build header.
    $row = array();
    foreach ($arg['header'] as $cell_key => $cell_value) {
      if ($cell_key !== 'attributes') {
        $row[$cell_key] = array(
          'inner' => $this->parseInner($cell_value),
          'attributes' => $this->parseAttributes($cell_value),
        );
      }
    }
    $arg['header'] = array(
      'inner' => array(
        array(
          'inner' => $row,
          'attributes' => array(),
        )
      ),
      'attributes' => $this->parseAttributes($arg['header']),
    );

    // Build caption.
    if (isset($arg['caption'])) {
      $arg['caption'] = array(
        'inner' => $this->parseInner($arg['caption']),
        'attributes' => $this->parseAttributes($arg['caption']),
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
              if (isset($col_value['attributes'])) {
                $col = array(
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
          'inner' => $colgroup,
          'attributes' => $this->parseAttributes($arg['colgroups'][$colgroup_key]),
        );
      }
      // We'll just implode these.
      $arg['colgroups'] = $colgroups;
    }

    parent::__construct($arg);
  }
}