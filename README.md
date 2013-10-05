# Object-Oriented Drupal RenderAPI

## Installing

You'll need to download [Silex](http://silex.sensiolabs.org/) via [Composer](http://getcomposer.org/):

    $> cd path/to/renderapi
    $> curl -sS https://getcomposer.org/installer | php
    $> php composer.phar install

## Getting started

* ./index.php returns some a sample renderables at various URLs.
* ./fake-drupal is just a dummy Drupal skeleton to give some conceptual basis. 
  In fake-drupal.php, explore un-commenting the lines in: `getAlterCallbacks()`,
  `getModuleDecoratorClasses()`, and `getThemeDecoratorClass()` to explore how
  extensibility via Builder and Decorator methods.

## Things we need

* Logical template-based markup that can be extended and overridden.
   * `hook_theme()`, PHPTemplate (Twig), `template_preprocess()`, hook
     suggestions, theme registry
* Abstracted, alterable structure.
   * Render arrays, `hook_node_view()`, `hook_page_alter()`
* Sensible, accessible, API.
   * `theme()`, `drupal_render()`, `render()`/`hide()`/`show()`

## Render workflow for D9

1. Define renderable. `$build = new RenderableBuilder('ThemeFoo', array('foo' => $foo));`
2. Alter renderable. `$build->set('param', $myParam); $build->setThemeClass('ThemeBar');`
3. Render renderable. `$renderable = $build->create(); $renderable instanceOf ThemeBar;`
   A. Prepare/alter vars. `$renderable = new BazThemeBarDecorator($renderable);`
   B. Invoke theme engine & template. `print (string) $renderable;`

## Wishlist

"Make it faster. Don't break caching." -- Moshe

## Changelog

* 2013-10-05 Drillable syntax via find() method to delegate from
  RenderableBuilder to Renderable.
* 2013-10-05 Weight support for RenderableBuilder.
* 2013-10-05 Silex and composer integration.
* 2013-09-25 Version 2.0 - Complete rewrite via Builder and Decorator patterns.
* 2012-12-03 Version 1.1 - Looser coupling with HTML tags, but still not quite
  there.
* 2012-11-30 Version 1.0 - Basically an abstracted HTML builder. Not really what
  we want.

## Acknowledgements

Big thanks to Mark Sonnabaum for OO reality checks and the the Drupal 8 Twig
team.
