<?php

namespace Pirminis;

/**
 * You have a variable, or a value. But you are not sure
 * if it is null, or not null. So that is what this library
 * is about: handling "maybe" situations in a simple, KISS way.
 *
 * Handles chainable callabes like I handle your mom.
 */
class Maybe
{
    protected $subject;

    /**
     * Assign test subject.
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * For situations with [chainable] callables.
     * @param  callable $method Method of a class/instance.
     * @param  array    $args   Method arguments.
     * @return Pirminis\Maybe
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->subject, $method)) {
            return new Maybe(null);
        }

        $this->subject = call_user_func_array([$this->subject, $method], $args);
        return $this;
    }

    /**
     * Get value.
     * @param  string $default Default value or something in case of null.
     * @return [type]          [description]
     */
    public function value($default = '', $use_empty = false)
    {
        if ($use_empty) {
            return empty($this->subject) ? $default : $this->subject;
        }

        return is_null($this->subject) ? $default : $this->subject;
    }
}
