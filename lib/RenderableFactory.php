<?php

/**
 * Factory class to digest our argument and return the appropriate parent
 * instance.
 */
class RenderableFactory {

  const RENDERABLE_TYPE_ARRAY = 'array';
  const RENDERABLE_TYPE_SCALAR = 'scalar';
  const RENDERABLE_TYPE_IGNORE = 'ignore';

  public function passiveType($type) {
    return in_array($type, array(
      RenderableFactory::RENDERABLE_TYPE_ARRAY,
      RenderableFactory::RENDERABLE_TYPE_SCALAR,
      RenderableFactory::RENDERABLE_TYPE_IGNORE
    ));
  }

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
    // @todo Consider this.
    // elseif (is_object($arg) && method_exists($arg, '__toString')) {}
    else {
      // Something we should ignore, i.e. Object, NULL, or Resource.
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

  /**
   * This should likely exist through some autodiscovery/hook/registry mechanism.
   */
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

  /**
   * Sometimes our #type and the HTML tag aren't the same. Bug? Feature? :)
   */
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
