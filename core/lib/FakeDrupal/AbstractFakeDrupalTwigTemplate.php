<?php

/**
 * @file Extends Twig_Template to leverage accessor
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FakeDrupal;

abstract class AbstractFakeDrupalTwigTemplate extends \Twig_Template {

    /**
     * Returns the attribute value for a given array/object.
     *
     * @param mixed   $value            Value usually sent as an argument to an accessor.
     * @param mixed   $key              Key in question for the given accessor
     * The following are not used at the moment but exist in the parent method.
     * @param array   $arguments         An array of arguments to pass if the item is an object method
     * @param string  $type              The type of attribute (@see Twig_Template constants)
     * @param Boolean $isDefinedTest     Whether this is only a defined check
     * @param Boolean $ignoreStrictCheck Whether to ignore the strict attribute check or not
     *
     * @return mixed The attribute value, or a Boolean when $isDefinedTest is true, or null when the attribute is not set and $ignoreStrictCheck is true
     *
     * @throws Twig_Error_Runtime if the attribute does not exist and Twig is running in strict mode and $isDefinedTest is false
     */
    protected function getAttribute($value, $key, array $arguments = array(), $type = \Twig_Template::ANY_CALL, $isDefinedTest = false, $ignoreStrictCheck = false)
    {

        if ($value instanceOf \RenderAPI\RenderableBuilderInterface) {
          return $value->find($key);
        }
        elseif ($value instanceOf \RenderAPI\RenderableInterface) {
          return $value->get($key);
        }
        elseif (is_array($value) && isset($value[$key])) {
          return $value[$key];
        }
        elseif ($value instanceOf \stdClass && isset($value->$key)) {
          return $value->$key;
        }
        else {
          throw new \Twig_Error_Runtime(sprintf('Cannot access key "%s"', $key), -1, $this->getTemplateName());
        }

    }

}
