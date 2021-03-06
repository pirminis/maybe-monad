<?php

// actual namespace
namespace Pirminis;

/**
 * You have a variable, or a value. But you are not sure
 * if it is null, or not null. So that is what this library
 * is about: handling "maybe" situations in a simple, KISS way.
 *
 * Handles chainable callabes like I handle your mom.
 */
abstract class Maybe implements \ArrayAccess
{
    protected $subject;

    /**
     * For situations with [chainable] callables.
     * @param  callable $method Method of a class/instance.
     * @param  array    $args   Method arguments.
     * @return Pirminis\Maybe
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->subject, $method)) {
            return \Maybe();
        }

        return \Maybe(call_user_func_array([$this->subject, $method], $args));
    }

    /**
     * For situations with properties.
     * @param  string $property Property of an object.
     * @return Pirminis\Maybe
     */
    public function __get($property)
    {
        if (!property_exists($this->subject, $property)) {
            return \Maybe();
        }

        return \Maybe($this->subject->{$property});
    }

    /**
     * Extract value from monad.
     * @param  string  $default    Default value or something in case of null.
     * @param  boolean $use_empty  Should we use 'empty' instead of 'isset'?
     * @return mixed
     */
    public function val($default = null, $use_empty = false)
    {
        if ($use_empty) {
            return empty($this->subject) ? $default : $this->subject;
        }

        return is_null($this->subject) ? $default : $this->subject;
    }

    public function offsetGet($offset)
    {
        return \Maybe(isset($this->subject[$offset]) ?
               $this->subject[$offset] :
               null);
    }

    public function offsetSet($offset, $value)
    {
        // monad should and will be immutable
    }

    public function offsetExists($offset)
    {
        return isset($this->subject[$offset]);
    }

    public function offsetUnset($offset)
    {
        // monad should and will be immutable
    }

    public function map(\Closure $closure)
    {
        $array = is_array($this->subject) ?
                 $this->subject :
                 [$this->subject];

        foreach ($array as $key => $value) {
            $closure_ret_val = $closure(\Maybe($value), \Maybe($key));
            $array[$key] = $closure_ret_val instanceof static ?
                           $closure_ret_val->val() :
                           $closure_ret_val;
        }

        return \Maybe(is_array($this->subject) ? $array : $array[0]);
    }
}
