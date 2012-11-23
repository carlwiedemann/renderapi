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
      '#type' => 'link',
      'attributes' => array(
        'href' => url('http://www.example.com'),
      ),
      'inner' => 'Learn more',
    );

    // Example: Image (with image styles)
    $arr = array(
      '#type' => 'image',
      '#style' => 'large', // Optional
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

#### @todo Objects

What could __toString need to know for nodes?

* Display mode

More generally, what could __toString need to know for objects?

* Display mode
* Class (entity)

Default display modes per Class (i.e. class Node)


### @todo Ignored types: NULL, Resource

For the sake of simplicity, we may simply ignore (for now) NULL and Resource,
they will render as empty strings.

### @todo Types of data: how they are render

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

@todo Add path information, update this

* common.inc:5812
* EntityListController.php:149
* EntityListControllerInterface.php:56
* EntityRow.php:136
* ViewListController.php:152
* Environment.php:293
* Template.php:245
* TemplateInterface.php:31
