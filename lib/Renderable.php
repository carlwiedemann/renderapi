<?php

// namespace RenderAPI\Core;

class Renderable extends RenderableBase {
  public $attributes;

  protected $cdata = FALSE;

  protected $names = array(
    'inner',
  );

  public function __construct($arg) {
    $this->cdata = !empty($arg['#cdata']);

    // Turn any given attributes into an attributes object.
    if (is_array($arg) && isset($arg['attributes'])) {
      $this->attributes = new Attribute($arg['attributes']);
    }
    else {
      $this->attributes = new Attribute(array());
    }

    // Assign variables.
    foreach ($this->names as $name) {
      if (isset($arg[$name])) {
        $this->$name = RenderableFactory::create($arg[$name]);
      }
      else {
        $this->$name = RenderableFactory::create(NULL);
      }
    }

    // If inner is not yet set, and we have given names, make inner as a
    // collection of names.
    if (!isset($this->inner)) {
      $inner = array();
      foreach ($this->names as $name) {
        $inner[$name] = &$this->$name;
      }
      if (!empty($inner)) {
        $this->inner = RenderableFactory::create($inner);
      }
    }
  }

  public function show() {
    $this->printed = FALSE;
    // @todo Consider what we were doing here :)
    // foreach ($this->names as $name) {
    //   $this->$name->show();
    // }
    // if (isset($this->inner) && !in_array('inner', $this->names)) {
    //   $this->inner->show();
    // }
    // if (isset($this->attributes)) {
    //   $this->attributes->show();
    // }
  }

  /**
   * Renderables will be built as concatenated strings, or invoke templates.
   */
  protected function setValue() {
    // Consider whether to delegate to Element builder or template engine.
    // See if the template is being overridden via the theme engine.
    if (!RenderableFactory::passiveType($this->type) && engine_override($this->type)) {
      $this->value = engine_output($this->type, $this);
    }
    else {
      // If the template is not being overridden, simply build as a default
      // element.
      $value = new RenderableElement(array(
        '#type' => $this->type,
        '#cdata' => $this->cdata,
        'inner' => $this->inner,
        'attributes' => $this->attributes,
      ));
      $this->value = (string) $value;
    }
  }

  // The top-level names for this particlar renderable. Mostly utilized by sub-
  // classes.
  function getNames() {
    $keys = array_merge(array('attributes', 'inner'), $this->names);
    $resulting_keys = array();
    foreach ($keys as $key) {
      if (isset($this->$key)) {
        $resulting_keys[] = $key;
      }
    }
    return $resulting_keys;
  }

}
