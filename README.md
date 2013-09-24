Some new experimentation.


//
// Get data.
// ---
// Define render array.
// Alter render array.
// Render render array.
//   Call theme callbacks.
//     Call preprocessors.
//     Invoke template.
// ---
// Send markup to client.
//

//
// Get data.
// ---
// Define renderable. $build = new RenderableBuilder('ThemeFoo', array('foo' => $foo));
// Alter renderable. $build->set('param', $myParam); $build->setThemeClass('ThemeBar');
// Render renderable. $build instanceOf ThemeBar; $build->render();
//   Prepare/alter vars. Decorate $build instanceOf ThemeBar.
//   Invoke template.
// ---
// Send markup to client.
//

//
// Things we need
// 
// Logical template-based markup that can be extended and overridden.
//    * hook_theme(), PHPTemplate (Twig), template_preprocess(), hook suggestions, theme registry.
// Abstracted, alterable structure.
//    * Render arrays, hook_node_view(), hook_page_alter()
// Sensible, accessible, API.
//    * theme(), drupal_render(), render()/hide()/show().
// 
// "Make it faster. Don't break caching."
//
