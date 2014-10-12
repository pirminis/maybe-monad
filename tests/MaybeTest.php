<?php

require('lib/Pirminis/Maybe.php');

use Pirminis\Maybe;

class MaybeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $maybe = new Maybe(null);

        $this->assertInstanceOf('Pirminis\Maybe', $maybe);
    }

    public function testEmptyValue()
    {
        $name = new Maybe(null);
        $expectedValue = 'no name';

        $this->assertSame($expectedValue, $name->val('no name'));
    }

    public function testStringValue()
    {
        $expectedValue = 'John';
        $name = new Maybe($expectedValue);

        $this->assertSame($expectedValue, $name->val('no name'));
    }

    public function testIntegerValue()
    {
        $expectedValue = 28;
        $age = new Maybe($expectedValue);

        $this->assertSame($expectedValue, $age->val(0));
    }

    public function testDoubleValue()
    {
        $expectedValue = 0.5;
        $fifty_fifty = new Maybe($expectedValue);

        $this->assertSame($expectedValue, $fifty_fifty->val(0.0));
    }

    public function testArray()
    {
        $expectedValue = ['one', 'two', 'three'];
        $list = new Maybe($expectedValue);

        $this->assertSame($expectedValue, $list->val([]));
    }

    public function testObject()
    {
        $expectedValue = new stdClass();
        $obj = new Maybe($expectedValue);

        $this->assertSame($expectedValue, $obj->val(new stdClass()));
    }

    public function testImmutability()
    {
        $expectedValue = new stdClass();
        $expectedObjectHash = spl_object_hash($expectedValue);
        $obj = new Maybe($expectedValue);

        $this->assertSame($expectedObjectHash,
                          spl_object_hash($obj->val(new stdClass())));
    }

    public function testInexistingMethod()
    {
        $expectedValue = 'no name';
        $obj = new Maybe(null);

        $this->assertSame($expectedValue,
                          $obj->getName()->val($expectedValue));
    }

    public function testExistingMethod()
    {
        $expectedValue = 'John';
        $user = new Maybe(new User($expectedValue));

        $this->assertSame($expectedValue, $user->getName()->val('no name'));
    }

    public function testInexistingMethodChain()
    {
        $expectedValue = 'no name';
        $order = new Maybe(null);

        $this->assertSame($expectedValue,
                          $order->getUser()->getName()->val($expectedValue));
    }

    public function testExistingMethodChain()
    {
        $expectedValue = 'John';
        $order = new Maybe(new Order(new User($expectedValue)));

        $this->assertSame($expectedValue,
                          $order->getUser()->getName()->val('no name'));
    }

    public function testValueUsingIsset()
    {
        $expectedValue = '';
        $string = new Maybe($expectedValue);

        $this->assertSame($expectedValue, $string->val('some value'));
    }

    public function testValueUsingEmpty()
    {
        $expectedValue = 'value is empty';
        $string = new Maybe('');

        $this->assertSame($expectedValue, $string->val($expectedValue, true));
    }

    public function testClosure()
    {
        $expectedValue = 'oh yeah!';
        $expectedCallback = function() use ($expectedValue) { return $expectedValue; };
        $callback = new Maybe($expectedCallback);

        $this->assertInstanceOf('\Closure', $callback->val());
        $this->assertSame($expectedCallback, $callback->val());
        $this->assertSame($expectedValue, $callback->val()->__invoke());
    }
}

class User
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}

class Order
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
