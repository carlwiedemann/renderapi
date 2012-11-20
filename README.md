Introduction
--------------------------------------------------------------------------------

Render API in Drupal needs a refresh. The system is inconsistent, vague, and
not well understood or documented.

Purpose of this document
--------------------------------------------------------------------------------

Define basic axioms by which to refactor RenderAPI. Provide possible syntax
examples and implementations for RenderAPI.

Goals for Render API
--------------------------------------------------------------------------------

* Everything a renderable
* Visible API
* Conventions (wrapper methods, protected namespaces)
* Array API, OO-driven internals

API data model
--------------------------------------------------------------------------------

Render API is a way of defining a structure of how we want format information.
We have a method render() that should receive a single argument that will then
be converted to a formatted string read by a user agent, i.e. HTML. We don't
plan to change this model, so let's define our first Axiom.

> Axiom i:
> render() receives a single argument.

One of the primary utilities of Render API in Drupal is that the arguments for
render may be changed and adjusted before being sent to the user agent. We don't
plan to change this either.

> Axiom ii:
> The render() argument may be altered (e.g. a render array) by modules.

### Types of data

Consider PHP primitive data types.

    Type name
    ---------
    String
    Int
    Bool
    Float
    Array
    Object
    NULL
    Resource
    

Let us consider how each of these would be regarded as being the argument of
render().

### Scalar string-like types: String, Int, Float, Bool

Some of these can simply be converted to a string and printed directly without
question, such as Float and Int. Let's just assume native methods here, this is,
hopefully, self-explanatory.

    // String
    $var = 'asdf';
    render($var);
    // "asdf"

    // Int
    $var = -997;
    render($var);
    // "-997"

    // Float
    $var = (sqrt(5) + 1)/2;
    render($var);
    // "1.6180339887499";

    // Bool
    $var = TRUE;
    render($var);
    // "1"
    
    $var = FALSE;
    render($var);
    // "0"

So, let us define our second axiom.

> Axiom iii:
> Int, Float, and Bool arguments will convert to String. Strings arguments will
> print natively.

@todo Consider formatting conventions sprintf, datestamps, etc.

### Compound types: Arrays, Objects

#### Arrays

Arrays are our primary method of defining structure, and are the tools by which
Drupal developers are most familiar. Therefore, we will consider arrays to be
the most common type of argument and the primary subject of this document.

Arrays in PHP can have two types of keys: numeric keys, and string-based
associative keys. Often in Drupal, associative-keyed arrays are used to describe
hierarchical information and properties, whereas numeric-keyed arrays are simply
lists or sets. This distinction is important, as we will discover.

Historically, render() receives an argument that is an array, more specifically
a "render array," the supposition being that a render array contains, at
minimum, two parameters:

1. Content of some sort
2. Information about how to format the content

Given this need, we shall assume that render arrays will _always_ have
associative keys since we need to distinguish content from information.

Let us then suppose for simplicity sake that if a numeric-keyed array is sent as
an _argument_ of render(), we'll simply recursively call on each element of the
array and concatenate the result.

    $arr = array('a', 'b', 'c');
    render($arr);
    // "abc"

(Obligatory Reiteration: Numeric-keyed arrays are only treated like this when an
_argument_ of render()).

Let us also establish that because all render arrays are associative does not
imply all associative array arguments are render arrays. Consider the following
example, in which we'll treat a simple associative array in the same manner as
the numeric array.

    $arr = array(
      'title' => "Hello World\n",
      'subtitle' => "Remove me\n",
      'body' => "Jackdaws love my big sphinx of quartz.",
    );
    
    render($arr);
    
    // "Hello World
    //  Remove me
    //  Jackdaws love my big sphinx of quartz."

Recalling Axiom ii, suppose this is to be altered to simply remove the subtitle
parameter. The function render() operates in a way to apply alter callbacks to
the argument by reference.

    function some_alter_callback(&$arr) {
      unset($arr['subtitle']);
    }
    
    $arr = array(
      'title' => "Hello World\n",
      'subtitle' => "Remove me\n",
      'body' => "Jackdaws love my big sphinx of quartz.",
    );
    
    render($arr); // Will apply alter callbacks.
    
    // "Hello World
    //  Jackdaws love my big sphinx of quartz."

As we can see, alter callbacks possibly need to target specific argument
parameters.

Let us then establish the following axioms:

> Axiom iv:
> An array that is not a render array is treated simply as a list of arguments.

> Axiom v:
> A render array is an associative array. An associative array is not
> necessarily a render array.

We'll revisit the render array definition later.

#### Defining a render array

Given Axiom v, we need a way for render arrays to distinguish themselves from
associative non-render arrays. We can accomplish this with a specific
hash-prefixed key. Hash-prefixed keys have been used in Form API to
distinguish form element properties from form element hierarchy.

So, recall that a render array contains:

1. Content of some sort
2. Information about how to format the content

Let's distinguish these using hash-prefix keys so that the array does contain:

1. Content of some sort, _defined by non-hash-prefixed keys_
2. Information about how to format the content, _defined by hash-prefixed keys_

This differs a bit from how Render API historically worked: that is,
hashed-prefixed keys were used everywhere; there wasn't a clear distinction if
they were content or information about the content. This was confusing.

A further point of confusion was that the #type key was only used for form
elements, and yet #theme was used for render arrays. From an API perspective, we
could instead apply default theme callbacks to particular #type values that can
be altered or overridden. In this way #type provides _semantic_ information
about the render array whereas #theme does not (it's just a callback).

Therefore, let us define a render array using the hash-prefixed key #type.

> Axiom vi:
> A render array requires the key #type.

Remaining associative keys thus, do not need hash-prefixes since they will be
expected by the associated theme function implementation. Principally speaking,
one may think of these variables as being tied to addressability in via a
theme's templates.

    // Example: Table
    $arr = array(
      '#type' => 'table',
      'attributes' => array(
        'class' => array('admin','stats'),
        'data-foo' => 'bar' 
      ),
      'header => array(
        'First',
        'Second',
        'Third'
      ),
      'rows' => array(
        array(1, 2, 3),
        array(4, 5, 6)
      ),
    );

    // Example: Ordered list
    $arr = array(
      '#type' => 'ol',
      'items' => array(
        'First',
        'Second',
        'Third',
      ),
    );

    // Example: Unordered lists
    $arr = array(
      '#type' => 'ul',
      'items' => array(
        'First',
        'Second',
        'Third',
      ),
    );

    // Example: Link
    $arr = array(
      '#type' => 'a',
      'attributes' => array(
        'href' => url('http://www.example.com'),
      ),
      'text' => 'Learn more',
    );

    // Example: Image (with image styles)
    $arr = array(
      '#type' => 'img',
      '#image_style' => 'large', // Optional
      'attributes' => array(
        'src' => path_to_file('public://some-image.png'),
      ),
    );

@todo Other formatters
Text w/ summary
Comma delimited
File list
File table
File URL

Protected keys

* inner
* attributes


#### Objects

@todo

return array($node);

render($node);

print $node;

$output = $node->toString();
print $output;

What does __toString need to know for nodes?
Display mode
Node type

More generally, what does __toString need to know for objects?
Display mode
Object Class

Default display modes per Class (i.e. class Node)


### Ignored types: NULL, Resource

For the sake of simplicity, we may simply ignore (for now) NULL and Resource,
they will render as empty strings.

### Types of data: how they are render

How should these be treated as components of a renderable array? And what variations are expected?

    Type name   
    ----------------------------
    String      Native
    Int         Convert to String
    Bool        Convert to String
    Float       Convert to String
    Array       Delegate
    Object      __toString()
    NULL        Empty string
    Resource    Empty string

@todo Finish this

* Primitive
* Primary (traditional theme functions, single-purpose/independent)
* Component (new theme functions, multi-purpose/collective)
* Custom (extending our own)

@todo Discuss theme component library

@todo Discuss altering API

Appendix A. Resources
---------------------

### Historical documentation

* [Render Array documetation](http://drupal.org/node/930760 "Render Arrays in Drupal 7 | drupal.org")
* [Render Array Options](http://drupal.org/node/1776510#comment-6451178 "Render Array Options | drupal.org")
* [Default theme implementations | theme.api.php | Drupal 7 | Drupal API](http://api.drupal.org/api/drupal/modules%21system%21theme.api.php/group/themeable/7 "Default theme implementations | theme.api.php | Drupal 7 | Drupal API")
* [hook_element_info | system.api.php | Drupal 7 | Drupal API](http://api.drupal.org/api/drupal/modules%21system%21system.api.php/function/hook_element_info/7 "hook_element_info | system.api.php | Drupal 7 | Drupal API")
* [drupal_render | common.inc | Drupal 7 | Drupal API](http://api.drupal.org/api/drupal/includes%21common.inc/function/drupal_render/7 "drupal_render | common.inc | Drupal 7 | Drupal API")

### Calls to render in 8.x HEAD

@todo Add path information

* common.inc:5812
* EntityListController.php:149
* EntityListControllerInterface.php:56
* EntityRow.php:136
* ViewListController.php:152
* Environment.php:293
* Template.php:245
* TemplateInterface.php:31

### A list of theme functions

    ./core/includes/form.inc:function theme_select($variables) {
    ./core/includes/form.inc:function theme_fieldset($variables) {
    ./core/includes/form.inc:function theme_radio($variables) {
    ./core/includes/form.inc:function theme_radios($variables) {
    ./core/includes/form.inc:function theme_date($variables) {
    ./core/includes/form.inc:function theme_checkbox($variables) {
    ./core/includes/form.inc:function theme_checkboxes($variables) {
    ./core/includes/form.inc:function theme_container($variables) {
    ./core/includes/form.inc:function theme_tableselect($variables) {
    ./core/includes/form.inc:function theme_vertical_tabs($variables) {
    ./core/includes/form.inc:function theme_submit($variables) {
    ./core/includes/form.inc:function theme_button($variables) {
    ./core/includes/form.inc:function theme_image_button($variables) {
    ./core/includes/form.inc:function theme_hidden($variables) {
    ./core/includes/form.inc:function theme_textfield($variables) {
    ./core/includes/form.inc:function theme_email($variables) {
    ./core/includes/form.inc:function theme_tel($variables) {
    ./core/includes/form.inc:function theme_number($variables) {
    ./core/includes/form.inc:function theme_range($variables) {
    ./core/includes/form.inc:function theme_url($variables) {
    ./core/includes/form.inc:function theme_search($variables) {
    ./core/includes/form.inc:function theme_color($variables) {
    ./core/includes/form.inc:function theme_form($variables) {
    ./core/includes/form.inc:function theme_textarea($variables) {
    ./core/includes/form.inc:function theme_password($variables) {
    ./core/includes/form.inc:function theme_file($variables) {
    ./core/includes/form.inc:function theme_form_element($variables) {
    ./core/includes/form.inc:function theme_form_required_marker($variables) {
    ./core/includes/form.inc:function theme_form_element_label($variables) {
    ./core/includes/menu.inc:function theme_menu_tree($variables) {
    ./core/includes/menu.inc:function theme_menu_link(array $variables) {
    ./core/includes/menu.inc:function theme_menu_local_task($variables) {
    ./core/includes/menu.inc:function theme_menu_local_action($variables) {
    ./core/includes/menu.inc:function theme_menu_local_tasks(&$variables) {
    ./core/includes/pager.inc:function theme_pager($variables) {
    ./core/includes/pager.inc:function theme_pager_link($variables) {
    ./core/includes/theme.inc:function theme_get_registry($complete = TRUE) {
    ./core/includes/theme.inc:function theme($hook, $variables = array()) {
    ./core/includes/theme.inc:function theme_get_setting($setting_name, $theme = NULL) {
    ./core/includes/theme.inc:function theme_render_template($template_file, $variables) {
    ./core/includes/theme.inc:function theme_enable($theme_list) {
    ./core/includes/theme.inc:function theme_disable($theme_list) {
    ./core/includes/theme.inc:function theme_datetime($variables) {
    ./core/includes/theme.inc:function theme_status_messages($variables) {
    ./core/includes/theme.inc:function theme_link($variables) {
    ./core/includes/theme.inc:function theme_links($variables) {
    ./core/includes/theme.inc:function theme_dropbutton_wrapper($variables) {
    ./core/includes/theme.inc:function theme_image($variables) {
    ./core/includes/theme.inc:function theme_breadcrumb($variables) {
    ./core/includes/theme.inc:function theme_table($variables) {
    ./core/includes/theme.inc:function theme_tablesort_indicator($variables) {
    ./core/includes/theme.inc:function theme_mark($variables) {
    ./core/includes/theme.inc:function theme_item_list($variables) {
    ./core/includes/theme.inc:function theme_more_help_link($variables) {
    ./core/includes/theme.inc:function theme_feed_icon($variables) {
    ./core/includes/theme.inc:function theme_html_tag($variables) {
    ./core/includes/theme.inc:function theme_more_link($variables) {
    ./core/includes/theme.inc:function theme_progress_bar($variables) {
    ./core/includes/theme.inc:function theme_meter($variables) {
    ./core/includes/theme.inc:function theme_indentation($variables) {
    ./core/includes/theme.inc:function theme_get_suggestions($args, $base, $delimiter = '__') {
    ./core/includes/theme.maintenance.inc:function theme_task_list($variables) {
    ./core/includes/theme.maintenance.inc:function theme_install_page($variables) {
    ./core/includes/theme.maintenance.inc:function theme_update_page($variables) {
    ./core/includes/theme.maintenance.inc:function theme_authorize_report($variables) {
    ./core/includes/theme.maintenance.inc:function theme_authorize_message($variables) {
    ./core/modules/aggregator/aggregator.module:function theme_aggregator_block_item($variables) {
    ./core/modules/aggregator/aggregator.pages.inc:function theme_aggregator_categorize_items($variables) {
    ./core/modules/aggregator/aggregator.pages.inc:function theme_aggregator_summary_item($variables) {
    ./core/modules/aggregator/aggregator.pages.inc:function theme_aggregator_page_rss($variables) {
    ./core/modules/aggregator/aggregator.pages.inc:function theme_aggregator_page_opml($variables) {
    ./core/modules/book/book.admin.inc:function theme_book_admin_table($variables) {
    ./core/modules/color/color.module:function theme_color_scheme_form($variables) {
    ./core/modules/comment/comment.module:function theme_comment_block() {
    ./core/modules/comment/comment.module:function theme_comment_post_forbidden($variables) {
    ./core/modules/dblog/dblog.admin.inc:function theme_dblog_message($variables) {
    ./core/modules/field/field.form.inc:function theme_field_multiple_value_form($variables) {
    ./core/modules/field/field.module:function theme_field($variables) {
    ./core/modules/field/modules/link/link.module:function theme_link_formatter_link_separate($vars) {
    ./core/modules/field/modules/options/options.module:function theme_options_none($variables) {
    ./core/modules/field_ui/field_ui.admin.inc:function theme_field_ui_table($variables) {
    ./core/modules/file/file.field.inc:function theme_file_widget($variables) {
    ./core/modules/file/file.field.inc:function theme_file_widget_multiple($variables) {
    ./core/modules/file/file.field.inc:function theme_file_upload_help($variables) {
    ./core/modules/file/file.field.inc:function theme_file_formatter_table($variables) {
    ./core/modules/file/file.module:function theme_file_managed_file($variables) {
    ./core/modules/file/file.module:function theme_file_link($variables) {
    ./core/modules/file/file.module:function theme_file_icon($variables) {
    ./core/modules/filter/filter.admin.inc:function theme_filter_admin_overview($variables) {
    ./core/modules/filter/filter.admin.inc:function theme_filter_admin_format_filter_order($variables) {
    ./core/modules/filter/filter.module:function theme_text_format_wrapper($variables) {
    ./core/modules/filter/filter.module:function theme_filter_tips_more_info() {
    ./core/modules/filter/filter.module:function theme_filter_guidelines($variables) {
    ./core/modules/filter/filter.module:function theme_filter_html_image_secure_image(&$variables) {
    ./core/modules/filter/filter.pages.inc:function theme_filter_tips($variables) {
    ./core/modules/forum/forum.admin.inc:function theme_forum_form($variables) {
    ./core/modules/image/image.admin.inc:function theme_image_style_list($variables) {
    ./core/modules/image/image.admin.inc:function theme_image_style_effects($variables) {
    ./core/modules/image/image.admin.inc:function theme_image_style_preview($variables) {
    ./core/modules/image/image.admin.inc:function theme_image_anchor($variables) {
    ./core/modules/image/image.admin.inc:function theme_image_resize_summary($variables) {
    ./core/modules/image/image.admin.inc:function theme_image_scale_summary($variables) {
    ./core/modules/image/image.admin.inc:function theme_image_crop_summary($variables) {
    ./core/modules/image/image.admin.inc:function theme_image_rotate_summary($variables) {
    ./core/modules/image/image.field.inc:function theme_image_widget($variables) {
    ./core/modules/image/image.field.inc:function theme_image_formatter($variables) {
    ./core/modules/image/image.module:function theme_image_style($variables) {
    ./core/modules/language/language.admin.inc:function theme_language_admin_overview_form_table($variables) {
    ./core/modules/language/language.admin.inc:function theme_language_negotiation_configure_form($variables) {
    ./core/modules/language/language.admin.inc:function theme_language_negotiation_configure_browser_form_table($variables) {
    ./core/modules/locale/locale.pages.inc:function theme_locale_translate_edit_form_strings($variables) {
    ./core/modules/menu/menu.admin.inc:function theme_menu_admin_overview($variables) {
    ./core/modules/menu/menu.admin.inc:function theme_menu_overview_form($variables) {
    ./core/modules/node/content_types.inc:function theme_node_admin_overview($variables) {
    ./core/modules/node/node.module:function theme_node_search_admin($variables) {
    ./core/modules/node/node.module:function theme_node_recent_block($variables) {
    ./core/modules/node/node.module:function theme_node_recent_content($variables) {
    ./core/modules/node/node.pages.inc:function theme_node_add_list($variables) {
    ./core/modules/node/node.pages.inc:function theme_node_preview($variables) {
    ./core/modules/overlay/overlay.module:function theme_overlay_disable_message($variables) {
    ./core/modules/picture/picture.module:function theme_picture_formatter($variables) {
    ./core/modules/picture/picture.module:function theme_picture($variables) {
    ./core/modules/picture/picture.module:function theme_picture_source($variables) {
    ./core/modules/poll/poll.module:function theme_poll_choices($variables) {
    ./core/modules/rdf/rdf.module:function theme_rdf_template_variable_wrapper($variables) {
    ./core/modules/rdf/rdf.module:function theme_rdf_metadata($variables) {
    ./core/modules/shortcut/shortcut.admin.inc:function theme_shortcut_set_customize($variables) {
    ./core/modules/simpletest/simpletest.pages.inc:function theme_simpletest_test_table($variables) {
    ./core/modules/simpletest/simpletest.pages.inc:function theme_simpletest_result_summary($variables) {
    ./core/modules/system/lib/Drupal/system/Tests/Theme/HtmlTagUnitTest.php:   * Test function theme_html_tag()
    ./core/modules/system/lib/Drupal/system/Tests/Theme/ThemeTest.php:   * Test function theme_get_suggestions() for SA-CORE-2009-003.
    ./core/modules/system/system.admin.inc:function theme_system_date_time_settings($variables) {
    ./core/modules/system/system.admin.inc:function theme_admin_block($variables) {
    ./core/modules/system/system.admin.inc:function theme_admin_block_content($variables) {
    ./core/modules/system/system.admin.inc:function theme_admin_page($variables) {
    ./core/modules/system/system.admin.inc:function theme_system_admin_index($variables) {
    ./core/modules/system/system.admin.inc:function theme_status_report($variables) {
    ./core/modules/system/system.admin.inc:function theme_system_modules_fieldset($variables) {
    ./core/modules/system/system.admin.inc:function theme_system_modules_incompatible($variables) {
    ./core/modules/system/system.admin.inc:function theme_system_modules_uninstall($variables) {
    ./core/modules/system/system.admin.inc:function theme_system_themes_page($variables) {
    ./core/modules/system/system.admin.inc:function theme_system_date_format_localize_form($variables) {
    ./core/modules/system/system.module:function theme_system_powered_by() {
    ./core/modules/system/system.module:function theme_system_compact_link() {
    ./core/modules/system/system.module:function theme_confirm_form($variables) {
    ./core/modules/system/system.module:function theme_system_settings_form($variables) {
    ./core/modules/system/system.module:function theme_exposed_filters($variables) {
    ./core/modules/system/tests/modules/common_test/common_test.module:function theme_common_test_foo($variables) {
    ./core/modules/system/tests/modules/theme_page_test/theme_page_test.module:function theme_page_test_system_info_alter(&$info, $file, $type) {
    ./core/modules/system/tests/modules/theme_page_test/theme_page_test.module:function theme_page_test_system_theme_info() {
    ./core/modules/system/tests/modules/theme_test/theme_test.inc:function theme_theme_test($variables) {
    ./core/modules/system/tests/modules/theme_test/theme_test.module:function theme_test_theme($existing, $type, $theme, $path) {
    ./core/modules/system/tests/modules/theme_test/theme_test.module:function theme_test_system_theme_info() {
    ./core/modules/system/tests/modules/theme_test/theme_test.module:function theme_test_menu() {
    ./core/modules/system/tests/modules/theme_test/theme_test.module:function theme_test_init() {
    ./core/modules/system/tests/modules/theme_test/theme_test.module:function theme_test_hook_init_page_callback() {
    ./core/modules/system/tests/modules/theme_test/theme_test.module:function theme_test_template_test_page_callback() {
    ./core/modules/system/tests/modules/theme_test/theme_test.module:function theme_test_preprocess_html(&$variables) {
    ./core/modules/system/tests/modules/theme_test/theme_test.module:function theme_theme_test_foo($variables) {
    ./core/modules/taxonomy/taxonomy.admin.inc:function theme_taxonomy_overview_vocabularies($variables) {
    ./core/modules/taxonomy/taxonomy.admin.inc:function theme_taxonomy_overview_terms($variables) {
    ./core/modules/toolbar/toolbar.module:function theme_toolbar_toggle($variables) {
    ./core/modules/update/update.manager.inc:function theme_update_manager_update_form($variables) {
    ./core/modules/update/update.module:function theme_update_last_check($variables) {
    ./core/modules/update/update.report.inc:function theme_update_report($variables) {
    ./core/modules/update/update.report.inc:function theme_update_status_label($variables) {
    ./core/modules/update/update.report.inc:function theme_update_version($variables) {
    ./core/modules/user/user.admin.inc:function theme_user_admin_permissions($variables) {
    ./core/modules/user/user.admin.inc:function theme_user_permission_description($variables) {
    ./core/modules/user/user.admin.inc:function theme_user_admin_roles($variables) {
    ./core/modules/user/user.module:function theme_username($variables) {
    ./core/modules/user/user.module:function theme_user_signature($variables) {
    ./core/modules/views/lib/Drupal/views/Plugin/views/field/FieldPluginBase.php:  function theme($values) {
    ./core/modules/views/lib/Drupal/views/Plugin/views/field/FieldPluginBase.php:  public function themeFunctions() {
    ./core/modules/views/lib/Drupal/views/Plugin/views/PluginBase.php:  public function themeFunctions() {
    ./core/modules/views/tests/views_test_data/views_test_data.module:function theme_views_view_mapping_test($variables) {
    ./core/modules/views/theme/theme.inc:function theme_views_view_grouping($vars) {
    ./core/modules/views/theme/theme.inc:function theme_views_view_field($vars) {
    ./core/modules/views/theme/theme.inc:function theme_views_form_views_form($variables) {
    ./core/modules/views/theme/theme.inc:function theme_views_mini_pager($vars) {
    ./core/modules/views/views_ui/theme/theme.inc:function theme_views_ui_container($variables) {
    ./core/modules/views/views_ui/theme/theme.inc:function theme_views_ui_view_info($variables) {
    ./core/modules/views/views_ui/theme/theme.inc:function theme_views_ui_expose_filter_form($variables) {
    ./core/modules/views/views_ui/theme/theme.inc:function theme_views_ui_build_group_filter_form($variables) {
    ./core/modules/views/views_ui/theme/theme.inc:function theme_views_ui_reorder_displays_form($vars) {
    ./core/modules/views/views_ui/theme/theme.inc:function theme_views_ui_rearrange_form($variables) {
    ./core/modules/views/views_ui/theme/theme.inc:function theme_views_ui_rearrange_filter_form(&$vars) {
    ./core/modules/views/views_ui/theme/theme.inc:function theme_views_ui_style_plugin_table($variables) {
    ./core/modules/views/views_ui/theme/theme.inc:function theme_views_ui_view_preview_section($vars) {
    ./sites/all/modules/devel/devel.module:function theme_devel_querylog_row($variables) {
    ./sites/all/modules/devel/devel.module:function theme_devel_querylog($variables) {
    ./sites/all/modules/devel/devel_node_access.module:function theme_dna_permission($variables) {
