<?php

require('lib/Pirminis/Maybe.php');
require('lib/Pirminis/Some.php');
require('lib/Pirminis/None.php');
require('lib/global.php');

class MaybeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Law 1: left identity.
     *  - monad(arg).map(f) is monad
     *  - monad(arg).map(f) == monad(f(arg))
     */
    public function testMonadLaw1()
    {
        $f = function($v) {
            // why "\Maybe($v)" and not just "$v"?
            // because we will use "$f" as if it had nothing to do with monads.
            $number = \Maybe($v);
            return $number->is_some() ? \Maybe($v)->val() * 10 : \Maybe();
        };

        // with some value
        $age = 28;
        $left = \Maybe($age)->map($f);
        $right = \Maybe($f($age));

        $this->assertInstanceOf('\Pirminis\Some', $left->map($f));
        $this->assertInstanceOf('\Pirminis\Some', $right);
        $this->assertSame($left->val(), $right->val());

        // with none value
        $age = null;
        $left = \Maybe($age)->map($f);
        $right = \Maybe($f($age));

        $this->assertInstanceOf('\Pirminis\None', $left->map($f));
        $this->assertInstanceOf('\Pirminis\None', $right);
        $this->assertSame($left->val(), $right->val());
    }

    /**
     * Law 2. right identity.
     *  - monad(arg).map(function(v) { return v }) == monad(arg)
     */
    public function testMonadLaw2()
    {
        $f = function($v) {
            return $v->is_some() ? $v->val() * 100 : \Maybe();
        };

        $g = function($v) {
            return $v;
        };

        $this->assertInstanceOf('\Pirminis\Some', \Maybe(3.14)->map($f));
        $this->assertInstanceOf('\Pirminis\None', \Maybe()->map($f));

        // or

        $this->assertInstanceOf('\Pirminis\Some', \Maybe(3.14)->map($g));
        $this->assertInstanceOf('\Pirminis\None', \Maybe()->map($g));
        $this->assertSame(3.14, \Maybe(3.14)->map($g)->val(null));
        $this->assertSame(null, \Maybe()->map($g)->val(null));
    }

    /**
     * Law 3: associativity.
     *  - monad(arg).map(f).map(g) == monad(arg).map(f(g))
     *  - map(f) is monad, map(g) is monad, map(f(g)) is monad.
     */
    public function testMonadLaw3()
    {
        $g = function($v) {
            return \Maybe($v->val() / 100.0);
        };

        $f = function($v) {
            return \Maybe($v->val() * 2.0);
        };

        $fg = function ($v) use ($f, $g) {
            return $f($g($v));
        };

        $arg = 5000;
        $expectedValue = $arg * 2.0 / 100.0;
        $monad = \Maybe($arg);

        $left = $monad->map($f)->map($g);
        $right = $monad->map($fg);

        $this->assertSame($expectedValue, $left->val());
        $this->assertSame($expectedValue, $right->val());
        $this->assertSame($left->val(), $right->val());
        $this->assertInstanceOf('\Pirminis\Some', $left);
        $this->assertInstanceOf('\Pirminis\Some', $right);
    }

    public function testConstructingMonadFromMonadWillGiveMonad()
    {
        $expectedValue = 'this is just a string';

        $first_monad = \Maybe($expectedValue);
        $second_monad = \Maybe($first_monad);

        $this->assertSame($first_monad, $second_monad);
        $this->assertSame($first_monad->val(), $second_monad->val());
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Pirminis\None', \Maybe());
    }

    public function testEmptyValue()
    {
        $expectedValue = 'no name';

        $this->assertSame($expectedValue, \Maybe()->val($expectedValue));
    }

    public function testStringValue()
    {
        $expectedValue = 'John';

        $this->assertSame($expectedValue,
                          \Maybe($expectedValue)->val('no name'));
    }

    public function testIntegerValue()
    {
        $expectedValue = 28;

        $this->assertSame($expectedValue, \Maybe($expectedValue)->val(0));
    }

    public function testDoubleValue()
    {
        $expectedValue = 0.5;

        $this->assertSame($expectedValue, \Maybe($expectedValue)->val(0.0));
    }

    public function testArray()
    {
        $expectedValue = ['one', 'two', 'three'];

        $this->assertSame($expectedValue, \Maybe($expectedValue)->val([]));
    }

    public function testObject()
    {
        $expectedValue = new stdClass();

        $this->assertSame($expectedValue,
                          \Maybe($expectedValue)->val(new stdClass()));
    }

    public function testImmutability()
    {
        $expectedValue = new stdClass();
        $expectedObjectHash = spl_object_hash($expectedValue);

        $this->assertSame(
            $expectedObjectHash,
            spl_object_hash(\Maybe($expectedValue)->val(new stdClass())));
    }

    public function testInexistingMethod()
    {
        $expectedValue = 'no name';

        $this->assertSame($expectedValue,
                          \Maybe()->getName()->val($expectedValue));
    }

    public function testExistingMethod()
    {
        $expectedValue = 'John';
        $user = \Maybe(new User($expectedValue));

        $this->assertSame($expectedValue, $user->getName()->val('no name'));
    }

    public function testInexistingMethodChain()
    {
        $expectedValue = 'no name';

        $this->assertSame($expectedValue,
                          \Maybe()->getUser()
                                  ->getName()
                                  ->val($expectedValue));
    }

    public function testExistingMethodChain()
    {
        $expectedValue = 'John';
        $order = \Maybe(new Order(new User($expectedValue)));

        $this->assertSame($expectedValue,
                          $order->getUser()->getName()->val('no name'));
    }

    public function testValueUsingIsset()
    {
        $expectedValue = '';
        $string = \Maybe($expectedValue);

        $this->assertSame($expectedValue, $string->val('some value'));
    }

    public function testValueUsingEmpty()
    {
        $expectedValue = 'value is empty';

        $this->assertSame($expectedValue,
                          \Maybe('')->val($expectedValue, true));
    }

    public function testClosure()
    {
        $expectedValue = 'oh yeah!';
        $expectedCallback = function() use ($expectedValue) {
            return $expectedValue;
        };
        $callback = \Maybe($expectedCallback);

        $this->assertInstanceOf('\Closure', $callback->val());
        $this->assertSame($expectedCallback, $callback->val());
        $this->assertSame($expectedValue, $callback->val()->__invoke());
    }

    public function testEmptyArray()
    {
        $expectedValue = null;
        $maybeArray = \Maybe();

        $this->assertSame($expectedValue, $maybeArray['name']->val());
    }

    public function testMultiDimensionalArray()
    {
        $expectedValue = 'John';
        $maybeArray = \Maybe(['person' => ['name' => 'John', 'age' => 28]]);

        $this->assertSame($expectedValue, $maybeArray['person']['name']->val());
    }

    public function testIsSome()
    {
        $expectedValue = true;
        $data = '';

        $this->assertSame($expectedValue, \Maybe($data)->is_some());

        $expectedValue = true;
        $data = 'hello world!';

        $this->assertSame($expectedValue, \Maybe($data)->is_some());

        $expectedValue = true;
        $data = false;

        $this->assertSame($expectedValue, \Maybe($data)->is_some());

        $expectedValue = false;
        $data = null;

        $this->assertSame($expectedValue, \Maybe($data)->is_some());
    }

    public function testIsNone()
    {
        $expectedValue = false;
        $data = '';

        $this->assertSame($expectedValue, \Maybe($data)->is_none());

        $expectedValue = false;
        $data = 'hello world!';

        $this->assertSame($expectedValue, \Maybe($data)->is_none());

        $expectedValue = false;
        $data = false;

        $this->assertSame($expectedValue, \Maybe($data)->is_none());

        $expectedValue = true;
        $data = null;

        $this->assertSame($expectedValue, \Maybe($data)->is_none());
    }

    public function testSimpleMap()
    {
        $modifiedAge = \Maybe(28)->map(function($num) {
            return $num->val() * 10;
        });
        $expectedValue = 280;

        $this->assertSame($expectedValue, $modifiedAge->val());
    }

    public function testArrayMap()
    {
        $modifiedAge = \Maybe([12, 28, 68])->map(function($num) {
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
        $order = \Maybe(new Order(new User($expectedValue)));

        $name = $order->map(function($order) {
            return \Maybe($order)->getUser()->map(function($user) {
                return \Maybe($user)->getName()->map(function($name) {
                    return $name;
                });
            });
        });
        $name2 = $order->getUser()
                       ->getName()
                       ->map(function($name) use($expectedValue2) {
                           return $expectedValue2; });

        $this->assertSame($expectedValue, $name->val());
        $this->assertSame($expectedValue2, $name2->val());
    }

    public function testMapOnNullMonad()
    {
        $some = 'some';
        $none = 'none';

        $modifiedNull = \Maybe()->map(function($value) use ($some, $none) {
            return $value->is_some() ? $some : $none;
        });

        $this->assertSame($none, $modifiedNull->val($none));

        $modifiedNotNull = \Maybe('something')->map(
            function($value) use ($some, $none) {
                return $value->is_some() ? $some : $none; });

        $this->assertSame($some, $modifiedNotNull->val());
    }

    public function testNullValueIsInstanceOfNone()
    {
        $this->assertSame('Pirminis\None', get_class(\Maybe()));
    }

    public function testNonNullValueIsInstanceOfSome()
    {
        $this->assertSame('Pirminis\Some', get_class(\Maybe(23)));
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
