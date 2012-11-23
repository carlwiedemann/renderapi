<?php

// namespace RenderAPI\Core;

abstract class RenderableBase {

  protected $names = array();

  protected $type;

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

  static public function validKey($key) {
    return is_numeric($key) || !in_array($key,
      array(
        '#type',
        '#cdata',
        'attributes',
        'inner',
        'cdata',
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

  abstract protected function setValue();

}
