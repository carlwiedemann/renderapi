<?php

namespace RenderAPI;

/**
 * @file Decorator class to allow modules and themes to alter parameters for
 * templates.
 */
abstract class AbstractRenderableDecorator extends AbstractRenderable {

  /**
   * The object to decorate.
   *
   * @var AbstractRenderable
   */
  private $renderable;

  /**
   * Receive the renderable to decorate.
   *
   * @param AbstractRenderable
   * @return void
   */
  function __construct(AbstractRenderable $renderable) {
    $this->renderable = $renderable;
  }

  /**
   * Delegates to child.
   *
   * @return string
   */
  public function getBuildClass() {
    return $this->renderable->getBuildClass();
  }

  /**
   * Delegates to child.
   *
   * @return void
   */
  public function set($key, $value) {
    $this->renderable->set($key, $value);
  }

  /**
   * Delegates to child.
   *
   * @return mixed
   */
  public function get($key) {
    if ($this->exists($key)) {
      return $this->renderable->get($key);
    }
    else {
      $this->prepareOnce();
      return $this->exists($key) ? $this->renderable->get($key) : NULL;
    }
  }

  /**
   * Delegates to child.
   *
   * @return boolean
   */
  public function exists($key) {
    return $this->renderable->exists($key);
  }

  /**
   * Delegates to child.
   *
   * @return array
   */
  public function getAll() {
    return $this->renderable->getAll();
  }

  /**
   * Delegates to child.
   *
   * @return void
   */
  public function prepare() {
    $this->renderable->prepare();
  }

  /**
   * Delegates to child.
   *
   * @return string
   */
  public function getTemplateName() {
    return $this->renderable->getTemplateName();
  }

  /**
   * Delegates to child.
   *
   * @return string
   */
  public function isTemplateNameSet() {
    return $this->renderable->isTemplateNameSet();
  }

}