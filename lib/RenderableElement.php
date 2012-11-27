<?php

// namespace RenderAPI\Core;

/**
 * Arbitrary HTML tag builder.
 */
class RenderableElement extends RenderableBase {
  public $attributes;

  /**
   * Whether to wrap the inner contents in as CDATA.
   */
  protected $cdata = FALSE;

  protected $tag;

  public function __construct($arg) {
    $this->tag = RenderableFactory::tagLookup($arg['#type']);
    $this->cdata = !empty($arg['#cdata']);
    $this->inner = $arg['inner'];
    $this->attributes = $arg['attributes'];
  }

  public function show() {
    $this->printed = FALSE;
    if (isset($this->attributes)) {
      $this->attributes->show();
    }
  }

  protected function setValue($force = FALSE) {
    $inner = ((string) $this->inner);
    if ($inner !== '') {
      // Open tag.
      $output = '<' . $this->tag . ((string) $this->attributes) . '>';
      $inner_html = strpos($inner, '<') !== FALSE;
      // Consider CDATA.
      if ($this->cdata) {
        $output .= "\n<!--/*--><![CDATA[/*><!--*/\n" .  $inner . "\n/*]]>*/-->\n";
      }
      else {
        if ($inner_html) {
          $output .= "\n" . $inner;
        }
        else {
          $output .= $inner;
        }
      }
      // Close tag.
      $output .= '</' . $this->tag . ">\n";
      $this->value = $output;
    }
    else {
      // No inner value.
      $this->value = '<' . $this->tag . ((string) $this->attributes) . " />\n";
    }
  }

}
