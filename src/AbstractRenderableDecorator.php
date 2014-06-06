<?php

namespace RenderAPI;

/**
 * @file Decorator class to allow modules and themes to alter parameters for
 * templates.
 */
abstract class AbstractRenderableDecorator extends AbstractRenderable implements RenderableInterface {

  /**
   * The renderable to decorate.
   *
   * @var AbstractRenderable
   */
  private $child;

  /**
   * Sets renderable to decorate.
   *
   * @param AbstractRenderable
   * @return void
   */
  function __construct(AbstractRenderable $child) {
    $this->child = $child;
  }

  /**
   * Delegates to child.
   *
   * @return string
   */
  public function getBuildClass() {
    return $this->child->getBuildClass();
  }

  /**
   * Delegates to child.
   *
   * @return void
   */
  public function set($key, $value) {
    $this->child->set($key, $value);
  }

  /**
   * This will mark itself as prepared, even though it is pulling values
   * from the child.
   *
   * @return mixed
   */
  public function get($key) {
    if ($this->exists($key)) {
      return $this->child->get($key);
    }
    else {
      $this->prepareOnce();
      return $this->exists($key) ? $this->child->get($key) : NULL;
    }
  }

  /**
   * Delegates to child.
   *
   * @return boolean
   */
  public function exists($key) {
    return $this->child->exists($key);
  }

  /**
   * Delegates to child.
   *
   * @return array
   */
  public function getAll() {
    return $this->child->getAll();
  }

  /**
   * Delegates to child.
   *
   * @return array
   */
  public function getAllByWeight() {
    return $this->child->getAllByWeight();
  }

  /**
   * Delegates to child.
   *
   * @return array
   */
  public function getWeight() {
    return $this->child->getWeight();
  }

  /**
   * Delegates to child.
   *
   * @return array
   */
  public function isWeighted() {
    return $this->child->isWeighted();
  }

  /**
   * Delegates to child.
   *
   * @return void
   */
  public function prepare() {
    $this->child->prepare();
  }

  /**
   * Delegates to child.
   *
   * @return string
   */
  public function getTemplateName() {
    return $this->child->getTemplateName();
  }

  /**
   * Delegates to child.
   *
   * @return string
   */
  public function isTemplateNameSet() {
    return $this->child->isTemplateNameSet();
  }

}
