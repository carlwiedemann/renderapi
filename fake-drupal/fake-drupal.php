<?php

/**
 * @file I am fake Drupal.
 */

// Dummy load our renderable classes.
include './lib/RenderableBuilder.php';
include './lib/Renderable.php';
include './lib/RenderableDecorator.php';
include './lib/Accessor.php';

// Load our fake node module.
include './fake-drupal/modules/node/node.module.php';
include './fake-drupal/modules/node/ThemeFullNode.php';

// Load our fake custom module.
include './fake-drupal/modules/mymodule/mymodule.module.php';
include './fake-drupal/modules/mymodule/ThemeFoo.php';
include './fake-drupal/modules/mymodule/MyModuleFullNodeDecorator.php';

// Load fake common components.
include './fake-drupal/includes/theme/ThemeSomeExamples.php';
include './fake-drupal/includes/theme/ThemeItemList.php';
include './fake-drupal/includes/theme/ThemePage.php';

// Load our fake theme.
include './fake-drupal/themes/prague/PragueFullNodeDecorator.php';

/**
 * Dummy registry for callbacks that would alter our builder.
 */
function getAlterCallbacks($builder) {
  $callbacks = array();
  // switch ($builder->getBuildClass()) {
  //   case 'ThemeFullNode':
  //     $callbacks = array(
  //       'mymodule_alter_node_view',
  //     );
  //     break;
  //   case 'ThemeItemList':
  //     $callbacks = array(
  //       'mymodule_alter_item_list',
  //     );
  //     break;
  // }
  return $callbacks;
}

/**
 * Dummy registry for modules that may be decorating the renderable.
 */
function getModuleDecoratorClasses($renderable) {
  $classes = array();
  // switch ($renderable->getBuildClass()) {
  //   case 'ThemeFullNode':
  //     $classes = array(
  //       'MyModuleFullNodeDecorator',
  //     );
  //     break;
  // }
  return $classes;
}

/**
 * A registry for decorators that may apply to the renderable.
 */
function getThemeDecoratorClass($renderable) {
  $class = NULL;
  // switch ($renderable->getBuildClass()) {
  //   case 'ThemeFullNode':
  //   case 'MyModuleFullNodeDecorator':
  //     $class = 'PragueFullNodeDecorator';
  //     break;
  // }
  return $class;
}

/**
 * Dummy markup render mechanism.
 */
function render($build) {

  $markup = '';
  // Concatenate components
  if (is_array($build)) {

    render_sort($build);

    foreach ($build as $sub_build) {
      $markup .= render($sub_build);
    }
  }
  elseif (is_scalar($build) || $build instanceOf RenderableBuilder || $build instanceOf Renderable) {
    $markup = (string) $build;
  }

  // At this point, we are ready to render everything.
  return $markup;
}

function render_sort(&$builds) {
  $sortable = FALSE;

  foreach ($builds as $key => $build) {
    if (is_array($build)) {
      $builds[$key] = render_sort($build);
    }
    elseif ($build instanceOf RenderableBuilder) {
      // Check if weight parameter exists.
      if ($build->isWeighted()) {
        $sortable = TRUE;
      }
    }
  }

  if ($sortable) {
    uasort($builds, 'renderable_sort');
  }
}

function renderable_sort($a, $b) {
  $a_weight = 0;
  $b_weight = 0;
  if ($a instanceOf RenderableBuilder) {
    $a_weight = $a->getWeight();
  }
  if ($b instanceOf RenderableBuilder) {
    $b_weight = $b->getWeight();
  }
  if ($a_weight == $b_weight) {
    return 0;
  }
  return ($a_weight < $b_weight) ? -1 : 1;
}
