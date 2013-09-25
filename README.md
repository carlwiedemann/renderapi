# Some proof-of-concept for a Object-Oriented Drupal 9.x RenderAPI

* ./index.php pretends to build and return a sample renderable.
* ./fake-drupal is just a dummy Drupal skeleton to give some conceptual basis. 
  In fake-drupal.php, explore un-commenting the lines in: `getAlterCallbacks()`,
  `getModuleDecoratorClasses()`, and `getThemeDecoratorClass()` to explore how
  extensibility via Builder and Decorator methods.

## Things we need

* Logical template-based markup that can be extended and overridden.
   * `hook_theme()`, PHPTemplate (Twig), `template_preprocess()`, hook suggestions, theme registry
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

## Acknowledgements

Big thanks to Mark Sonnabaum for OO reality checks and the the Drupal 8 Twig
team.
