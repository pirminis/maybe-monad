<?php

require('lib/Pirminis/Maybe.php');
require('lib/Pirminis/Some.php');
require('lib/Pirminis/None.php');

use Pirminis\Maybe;

// 1. simple value
$maybeInteger = \Maybe(123);
var_dump($maybeInteger->val());
// result: int(123)

// 2. accessing array elements
$maybeArray = \Maybe(['person' => ['name' => 'John']]);
var_dump($maybeArray['person']['name']->val());
// result: string(4) "John"
var_dump($maybeArray['oh, oh.']['what']->val('nope'));
// result: string(4) "nope"

// 3. simple value that is null
var_dump(\Maybe(null)->val(0));
// result: int(0)


// 4. object
$obj = new stdClass();
$obj->title = 'Me so horny';

var_dump(\Maybe($obj)->val());
// result:
// class stdClass#2 (1) {
//   public $title =>
//   string(11) "Me so horny"
// }


// 5. existing property
$obj = new stdClass();
$obj->age = 28;

var_dump(\Maybe($obj)->age->val(0));
// result: int(28)


// 6. inexisting property
$obj = new stdClass();

var_dump(\Maybe($obj)->name->val('property does not exist'));
// result: string(23) "property does not exist"


// 7. chaining existing methods
class Order
{
    public function getUser()
    {
        return new User('John');
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

$order = new Order();

var_dump(\Maybe($order)->getUser()->getName()->val('no value'));
// result: string(4) "John"


// 8. chaining non existing methods
var_dump(\Maybe(null)->getUser()->getName()->val('no value'));
// result: string(8) "no value"


// 9. using "empty" instead of "isset"
$maybeNotEmptyString = \Maybe('');

var_dump($maybeNotEmptyString->val('empty'));
// result: string(0) ""
var_dump($maybeNotEmptyString->val('empty', true));
// result: string(3) "empty"


// 10. using 'map()' method of Maybe to adjust behaviour of (new) monad
$multipliedNumbers = \Maybe([1, 22, 45, 99])->map(function($num) {
    return $num->val() * 100;
});

var_dump($multipliedNumbers->val());
// result:
// array(4) {
//   [0] =>
//   int(100)
//   [1] =>
//   int(2200)
//   [2] =>
//   int(4500)
//   [3] =>
//   int(9900)
// }
var_dump($multipliedNumbers[3]->val());
// result: int(9900)

// you can also nest maps
$order = \Maybe(new Order(new User('John')));

$name = $order->map(function($order) {
    var_dump('Got order...');
    return $order->getUser()->map(function($user) {
        var_dump('Got user...');
        return $user->getName()->map(function($name) {
            var_dump('Got name...');
            return $name;
        });
    });
});

var_dump($name->val());
// result:
// string(12) "Got order..."
// string(11) "Got user..."
// string(11) "Got name..."
// string(4) "John"

// 11. you can also use methods 'is_some()' and 'is_none()' to simple test if
// monad's value is set (some) or empty (none)
$name = \Maybe(null);
var_dump($name->is_some());
// bool(false)
var_dump($name->is_none());
// bool(true)

$age = \Maybe(28);
var_dump($age->is_some());
// bool(true)
var_dump($age->is_none());
// bool(false)
