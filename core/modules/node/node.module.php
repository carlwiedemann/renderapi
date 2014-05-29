<?php

/**
 * @file Fake node module.
 */

/**
 * Load a dummy node.
 */
function node_load($nid) {
  return (object) array(
    'nid' => $nid,
    'title' => 'I am node ' . $nid,
  );
}
