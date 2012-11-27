<?php

// namespace RenderAPI\Core;

abstract class RenderableBase {

  /**
   * Top-level variables that the Renderable will make available in the
   * template.
   */
  protected $names = array();

  /**
   * The #type argument.
   */
  protected $type;

  /**
   * Setter
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * The inner contents, Mixed type.
   */
  public $inner;

  /**
   * The value of the Renderable when printed. Will be shown via __toString().
   */
  protected $value;

  /**
   * Whether the instance has been printed or not.
   */
  protected $printed = FALSE;
  
  /**
   * Return the printed value.
   */
  public function printed() {
    return $this->printed;
  }
  
  /**
   * Treat the instance and all subelements as unprinted.
   */
   public function show() {
     $this->printed = FALSE;
   }

  /**
   * Treat the instance and all subelements as printed.
   */
  public function hide() {
    $this->printed = TRUE;
  }

  /**
   * User-provided top-level keys shouldn't collide with our parameters.
   */
  static public function validKey($key) {
    return is_numeric($key) || !in_array($key,
      array(
        'type',
        'cdata',
        'attributes',
        'inner',
        'value',
        'printed',
      )
    );
  }

  /**
   * This should return our value.
   */
  public function __toString() {
    if (!$this->printed()) {
      // Create our value if it isn't created yet.
      $this->setValue();
      $return = $this->value;
      $this->hide();
    }
    else {
      $return = '';
    }
    return $return;
  }

  /**
   * Will vary whether we are a collection, render array, scalar, or element.
   */
  abstract protected function setValue();

}
