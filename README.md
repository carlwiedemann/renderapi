Introduction
--------------------------------------------------------------------------------

Render API in Drupal needs a refresh. The system is inconsistent, vague, and
not well understood or documented.

tl;dr
--------------------------------------------------------------------------------

If you want to see an example in action, look at these. The example uses
something similar to phptemplate even though we are using Twig in D8.

1. ./index.php
2. ./engines/phptemplate/templates/table.tpl.php

If you want to then see the internals, look at

1. ./lib/Table.php (for specific example)
2. ./lib/Renderable*

Status of this document
--------------------------------------------------------------------------------

A proof of concept exists, looking to attempt a patch to Drupal 8.x HEAD.
Various considerations to existing APIs and performance should be considered.

Purpose of this document
--------------------------------------------------------------------------------

Define basic axioms by which to refactor RenderAPI. Provide possible syntax
examples and implementations for RenderAPI.

Goals for Render API
--------------------------------------------------------------------------------

* Hierarchical approach
* Visible API
* Conventions (wrapper methods, reserved keys)
* OO-driven internals

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

### Ignored types: NULL, Resource

For the sake of simplicity, we may simply ignore (for now) NULL and Resource,
they will render as empty strings.

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

Let us assume, generally, that #type will correspond to a given template in our
theme, so that #type => 'table' indicates table.tpl.php should be used, and that
every #type should correspond to a given template file.

Generally speaking, let's reserve two other keys for render arrays:
**attributes**, used for HTML attributes, and **inner**, which describes the
inner content of some part of the renderable. These will not be hash-prefixed.

    // Example: Link
    $arr = array(
      '#type' => 'link',
      'attributes' => array(
        'href' => url('http://www.example.com'),
      ),
      'inner' => 'Learn more',
    );
    // Produces: <a href="http://www.example.com">Learn more</a>

These keys are handled specially: _attributes_ is converted into an Attributes
object, and _inner_ acts recursively such that render arrays may be nested with
other render arrays.

Remaining keys in the render array will be used to specify the content and
information of the template file. For a given #type, these keys will exist that
help to define the internal content of the renderable. For example, if we were
theming a table of data, we'd perhaps want to define the **header**, **rows**,
and **caption** and have the internals figure out the rest of the markup
construction. This principle will seem familiar as it was the DX of Drupal's
traditional theme() functions: provide a constrained set of arguments, let the
arguments be preprocessed and constructed into HTML elsewhere in a template (or
concatenation).

Therefore, we can expect that when provided a particular #type, we'll have
expected keys and expected markup. This, restated:

> Axiom vii: For a given #type, the internal structure should be well-defined.

For a given #type, these developer-friendly keys arguments will be refered to as
a _primary abstraction_. The internals will then rework these to turn them into
a _base abstraction_ (composed only of _attributes_ and _inner_). This
conversion step provides a drillable hierarchy.

Let's look at an example. Here's some markup for a table.

    <!-- Example: Table -->
    <table class="admin">
      <thead>
        <tr>
          <th>First</th><th>Second</th><th>Third</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td><td>2</td><td>3</td>
        </tr>
        <tr>
          <td>4</td><td>5</td><td>6</td>
        </tr>
      </tbody>
    </table>

Here's the primary abstraction. This is what we'd define as our render array. It
should look familiar.

    // Example: Table, primary abstraction
    $arr = array(
      '#type' => 'table',
      'attributes' => array(
        'class' => array('admin'),
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

Here, then, is what this would be converted to, in the form of a base
abstraction.

    // Example: Table, base abstraction
    $arr = array(
      '#type' => 'table',
      'attributes' => array(
        'class' => array('admin'),
      ),
      'inner' => array(
        array(
          'inner' => array(
            'inner' => array(
              array(
                'inner' => 'First',
              ),
              array(
                'inner' => 'Second',
              ),
              array(
                'inner' => 'Third',
              )
            ),
          ),
        array(
          'inner' => array(
            array(
              'inner' => array(
                array(
                  'inner' => '1',
                ),
                array(
                  'inner' => '2',
                ),
                array(
                  'inner' => '3',
                )
              ),
            ),
            array(
              'inner' => array(
                array(
                  'inner' => '1',
                ),
                array(
                  'inner' => '2',
                ),
                array(
                  'inner' => '3',
                )
              ),
            ),
          ),
        ),
      ),
    );

Finally, here is what the template (could) look like. Here, the function r()
indicates "render and print" though the final syntax of render api may be
different (i.e. Twig provides this automatically).

    <table<?php r($attributes); ?>>
      <thead<?php r($header->attributes); ?>>
      <?php foreach ($header->inner as $row): ?>
        <tr<?php r($row->attributes); ?>>
        <?php foreach ($row->inner as $cell): ?>
          <th<?php r($cell->attributes); ?>><?php r($cell->inner); ?></th>
        <?php endforeach ?>
        </tr>
      <?php endforeach ?>
      </thead>
      <tbody<?php r($rows->attributes); ?>>
        <?php foreach ($rows->inner as $row): ?>
          <tr<?php r($row->attributes); ?>>
            <?php foreach ($row->inner as $cell): ?>
              <td<?php r($cell->attributes); ?>><?php r($cell->inner); ?></td>
            <?php endforeach ?>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>

It is clear that a primary abstraction provides a much cleaner (and functional)
API for developers while the base abstraction provides a drillable structure
in the template. Notice that in the base abstraction, there is nothing that
clarifies the #type of each item in the hierarchy aside from 'table' at the very
top -- this comes from Axiom vii, we have a well-defined structure for a #table
type.

Primary abstractions look very similar to traditional theme functions, but
unlike traditional theme functions, they do not build markup directly or
immediately. They are _alterable_, so that other modules can refine output for
a given callback.

Another example, here's a primary abstraction of an ordered list:

    // Example: Ordered list
    $arr = array(
      '#type' => 'ol',
      'items' => array(
        'First',
        'Second',
        'Third',
      ),
    );

We can also consider metadata for the render array -- this is information that
doesn't necessarily have a markup or content equivalent, but tells us something
to execute when building the primary abstraction. Much like #type, we should
hash-prefix these to distinguish them as information vs content.

For example, when rendering an image, we could provide a #style key that would
describe the given style.

    // Example: Image (with image styles)
    $arr = array(
      '#type' => 'image',
      '#style' => 'large', // Optional
      'attributes' => array(
        'src' => path_to_file('public://some-image.png'),
      ),
    );

From our earlier table example, we could have an #empty key that would provide
some text in the table when there are now rows. Notice that these hash prefixed
keys don't represent _content_ but _information about the content_.

> Axiom viii: Metadata in a render array is provided by #hash-prefixed keys.
> Content in a render array is provided by non-#hash-prefixed keys.

#### Objects

There is some speculation that the __toString() method of first-citizen Objects
(like nodes and users) could be established to be used directly in a renderable
fashion. This seems potentially confusing at the current time of this writing,
so for now, let us assume that if an given content-related Object has a
__toString method, it will be evaluated as-is, though render api won't make any
special considerations for this behavior.

Execution steps
--------------------------------------------------------------------------------

See [https://dl.dropbox.com/u/21427810/render.pdf](https://dl.dropbox.com/u/21427810/render.pdf)
for a diagram.

Here's a high-level overview of what occurs from building the render array to 
viewing the final markup.

1. A renderable argument is returned in a page callback. This could be a scalar,
render array, or array of renderable arguments.
2. The argument is passed to RenderableFactory::create(), which checks to see
the data type of the argument and delegates the argument to a class constructor,
which may include RenderableScalar (for scalars), RenderableCollection, (for
non-render arrays), Renderable (for render arrays), or a subclass of Renderable
(such as Table). These specific classes offer different method variations to
deal with the different data types.
3. The constructor fleshes-out arguments, including attribute conversion,
converting primary abstractions into base abstractions. Base abstractions are
important for template addressiblity.
4. In the case of Renderable sub-classes, top-level variables are created based
on a defined set. For example, Table provides ->caption, ->colgroups, ->header,
->rows. By default, this is simply ->inner, and in the absence of ->inner, the
top-level variables constitute ->inner. Top-level variables and ->inner are
separately cast as Renderable objects via RenderableFactory::create(). Recursion
ensues as long as necessary.
5. The resulting top-level Renderable object will have public member variables
as other Renderables. Each one of these contains a __toString() method.
6. The Renderables stay as-is until printed individually, at which point the
__toString() method establishes the value of the Renderable. For a render array
with a given #type the output is delegated to a template called via the
established theme engine and the markup is returned.

Example
--------------------------------------------------------------------------------

Please see source of index.php for an example with a table.

@todo
--------------------------------------------------------------------------------

* Primitive (e.g. array, scalar, ignore)
* Base (e.g. div (not encouraged, but available))
* Primary (traditional theme functions, single-purpose/independent)
* Component (new theme functions, multi-purpose/collective)
* Custom (extending our own)

@todo Discuss theme component library. We'll take inspiration from Field UI,
Views, Display Suite, and other layout-driven tools to determine what sorts of
 #type parameters may be useful in addition to our most traditional theme
functions.

@todo Discuss altering API

Appendix A. Axioms
--------------------------------------------------------------------------------

* Axiom i: render() receives a single argument.
* Axiom ii: The render() argument may be altered (e.g. a render array) by
  modules.
* Axiom iii: Int, Float, and Bool arguments will convert to String. Strings
  arguments will print natively.
* Axiom iv: An array that is not a render array is treated simply as a list of
  arguments.
* Axiom v: A render array is an associative array. An associative array is not
  necessarily a render array.
* Axiom vi: A render array requires the key #type.
* Axiom vii: For a given #type, the internal structure should be well-defined.
* Axiom viii: Metadata in a render array is provided by #hash-prefixed keys.
  Content in a render array is provided by non-#hash-prefixed keys.

Appendix B. Types of data: how they are render
--------------------------------------------------------------------------------

    Type name   When printed
    ----------------------------------------------------------------------
    String      Native
    Int         Convert to String
    Bool        Convert to String
    Float       Convert to String
    Array       Delegate as Renderable class, use __toString()
    Object      If __toString() exists, convert to string, otherwise empty
    NULL        Empty string
    Resource    Empty string

Appendix C. Resources
--------------------------------------------------------------------------------

### Historical documentation

* [Render Array documetation](http://drupal.org/node/930760 "Render Arrays in Drupal 7 | drupal.org")
* [Render Array Options](http://drupal.org/node/1776510#comment-6451178 "Render Array Options | drupal.org")
* [Drupal API: Default theme implementations](http://api.drupal.org/api/drupal/modules%21system%21theme.api.php/group/themeable/7 "Default theme implementations | theme.api.php | Drupal 7 | Drupal API")
* [Drupal API: hook_element_info](http://api.drupal.org/api/drupal/modules%21system%21system.api.php/function/hook_element_info/7 "hook_element_info | system.api.php | Drupal 7 | Drupal API")
* [Drupal API: drupal_render](http://api.drupal.org/api/drupal/includes%21common.inc/function/drupal_render/7 "drupal_render | common.inc | Drupal 7 | Drupal API")

### @todo Calls to render in 8.x HEAD?

@todo Add path information, update this

* common.inc:5812
* EntityListController.php:149
* EntityListControllerInterface.php:56
* EntityRow.php:136
* ViewListController.php:152
* Environment.php:293
* Template.php:245
* TemplateInterface.php:31
