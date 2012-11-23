<?php

class RenderableFactory {

  const RENDERABLE_TYPE_ARRAY = 'array';
  const RENDERABLE_TYPE_SCALAR = 'scalar';
  const RENDERABLE_TYPE_IGNORE = 'ignore';

  /**
   * Convert the renderable if necessary.
   */
  public function create($arg) {

    if ($arg instanceOf RenderableBase) {
      return $arg;
    }

    if (is_array($arg)) {
      if (isset($arg['#type'])) {
        // A Renderable array, which we will delegate to a separate class.
        $type = $arg['#type'];
      }
      else {
        // A RenderableCollection, simply an array of renderables.
        $type = RenderableFactory::RENDERABLE_TYPE_ARRAY;
      }
    }
    elseif (is_scalar($arg)) {
      // A scalar, that can be convered to a string directly.
      $type = RenderableFactory::RENDERABLE_TYPE_SCALAR;
      $arg = (string) $arg;
    }
    else {
      // Something we should ignore, i.e. NULL, or Resource.
      $type = RenderableFactory::RENDERABLE_TYPE_IGNORE;
      $arg = '';
    }

    // The value of our renderable will often wrap in a tag.
    $class = RenderableFactory::classLookup($type);
    $return = new $class($arg);

    // Set type
    $return->setType($type);

    return $return;
  }

  static public function classLookup($type) {
    $class = 'Renderable';
    switch ($type) {
      case RenderableFactory::RENDERABLE_TYPE_SCALAR:
      case RenderableFactory::RENDERABLE_TYPE_IGNORE:
        $class = 'RenderableScalar';
        break;
      case RenderableFactory::RENDERABLE_TYPE_ARRAY:
        $class = 'RenderableCollection';
        break;
      case 'table':
        $class = 'Table';
        break;
      // case 'image':
      //   $class = 'Image';
      //   break;
    }
    return $class;
  }

  static public function tagLookup($type) {
    $tag = $type;
    switch ($type) {
      case 'link':
        $tag = 'a';
        break;
      case 'image':
        $tag = 'img';
        break;
    }
    return $tag;
  }

}
