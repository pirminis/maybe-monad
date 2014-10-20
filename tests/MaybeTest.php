<?php

require('lib/Pirminis/Maybe.php');

use Pirminis\Maybe;

class MaybeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Law 1: map() acts approximately as a neutral element.
     * monad(arg).map(f).val == f[arg]
     */
    public function testMonadLaw1()
    {
        $f = function($v) {
            // why "(new Maybe($v))" and not just "$v"?
            // because we will use "$f" as if it had nothing to do with monads.
            return (new Maybe($v))->val() * 10;
        };

        $arg = 17;
        $monad = new Maybe($arg);

        $this->assertSame($monad->map($f)->val(), $f($arg));
    }

    /**
     * Law 2. monad(arg).map(f) is monad.
     */
    public function testMonadLaw2()
    {
        $f = function($v) {
            return $v->val() * 100;
        };

        $monad = new Maybe(3.14);

        $this->assertInstanceOf('\Pirminis\Maybe', $monad->map($f));
    }

    /**
     * Law 3: monad(arg).map(f).map(g) == monad(arg).map(f(g))
     * map(f) is monad, map(g) is monad, hence map(f(g)) is monad.
     */
    public function testMonadLaw3()
    {
        $g = function($v) {
            return new Maybe($v->val() / 100.0);
        };

        $f = function($v) {
            return new Maybe($v->val() * 2.0);
        };

        $fg = function ($v) use ($f, $g) {
            return $f($g($v));
        };

        $arg = 5000;
        $expectedValue = $arg * 2.0 / 100.0;
        $monad = new Maybe($arg);

        $left = $monad->map($f)->map($g);
        $right = $monad->map($fg);

        $this->assertSame($expectedValue, $left->val());
        $this->assertSame($expectedValue, $right->val());
        $this->assertSame($left->val(), $right->val());
        $this->assertInstanceOf('\Pirminis\Maybe', $left);
        $this->assertInstanceOf('\Pirminis\Maybe', $right);
    }

    public function testConstructingMonadFromMonadWillGiveMonad()
    {
        $expectedValue = 'this is just a string';

        $first_monad = new Maybe($expectedValue);
        $second_monad = new Maybe($first_monad);

        // due to PHP limitation we cannot return same monad passed into
        // constructor, but we can compare values of both objects
        // $this->assertSame($first_monad, $second_monad);

        $this->assertSame($first_monad->val(), $second_monad->val());
    }

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

    public function testEmptyArray()
    {
        $expectedValue = '';
        $maybeArray = new Maybe(null);

        $this->assertSame($expectedValue, $maybeArray['name']->val());
    }

    public function testMultiDimensionalArray()
    {
        $expectedValue = 'John';
        $maybeArray = new Maybe(['person' => ['name' => 'John', 'age' => 28]]);

        $this->assertSame($expectedValue, $maybeArray['person']['name']->val());
    }

    public function testSome()
    {
        $expectedValue = true;
        $data = '';
        $value = new Maybe($data);

        $this->assertSame($expectedValue, $value->some());

        $expectedValue = true;
        $data = 'hello world!';
        $value = new Maybe($data);

        $this->assertSame($expectedValue, $value->some());

        $expectedValue = true;
        $data = false;
        $value = new Maybe($data);

        $this->assertSame($expectedValue, $value->some());

        $expectedValue = false;
        $data = null;
        $value = new Maybe($data);

        $this->assertSame($expectedValue, $value->some());
    }

    public function testNone()
    {
        $expectedValue = true;
        $data = '';
        $value = new Maybe($data);

        $this->assertSame($expectedValue, $value->none());

        $expectedValue = false;
        $data = 'hello world!';
        $value = new Maybe($data);

        $this->assertSame($expectedValue, $value->none());

        $expectedValue = true;
        $data = false;
        $value = new Maybe($data);

        $this->assertSame($expectedValue, $value->none());

        $expectedValue = true;
        $data = null;
        $value = new Maybe($data);

        $this->assertSame($expectedValue, $value->none());
    }

    public function testSimpleMap()
    {
        $age = new Maybe(28);
        $modifiedAge = $age->map(function($num) {
            return $num->val() * 10;
        });
        $expectedValue = 280;

        $this->assertSame($expectedValue, $modifiedAge->val());
    }

    public function testArrayMap()
    {
        $ages = new Maybe([12, 28, 68]);
        $modifiedAge = $ages->map(function($num) {
            return $num->val() / 4.0;
        });
        $expectedValues = [3.0, 7.0, 17.0];

        $this->assertSame($expectedValues[0], $modifiedAge[0]->val());
        $this->assertSame($expectedValues[1], $modifiedAge[1]->val());
        $this->assertSame($expectedValues[2], $modifiedAge[2]->val());
    }

    public function testNestedObjectsAndNestedMaps()
    {
        $expectedValue = 'John';
        $expectedValue2 = 'Peter';
        $order = new Maybe(new Order(new User($expectedValue)));

        $name = $order->map(function($order) {
            return (new Maybe($order))->getUser()->map(function($user) {
                return (new Maybe($user))->getName()->map(function($name) {
                    return $name;
                });
            });
        });
        $name2 = $order->getUser()->getName()->map(function($name) use($expectedValue2) {
            return $expectedValue2;
        });

        $this->assertSame($expectedValue, $name->val());
        $this->assertSame($expectedValue2, $name2->val());
    }

    public function testMapOnNullMonad()
    {
        $some = 'some';
        $none = 'none';

        $null = new Maybe(null);
        $modifiedNull = $null->map(function($value) use ($some, $none) {
            return $value->some() ? $some : $none;
        });

        $this->assertSame($none, $modifiedNull->val(null));

        $notNull = new Maybe('something');
        $modifiedNotNull = $notNull->map(function($value) use ($some, $none) {
            return $value->some() ? $some : $none;
        });

        $this->assertSame($some, $modifiedNotNull->val());
    }

    public function testExtensionOfMaybeClass()
    {
        $expectedValue = 'John';
        $name = new Option($expectedValue);

        $this->assertSame($expectedValue, $name->val());
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

class Option extends Maybe {};
