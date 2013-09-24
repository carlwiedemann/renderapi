<?php

interface RenderableInterface {
  function prepare();
  static function getRegisteredTemplate();
}
