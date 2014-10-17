<?php

require('lib/Pirminis/Maybe.php');

use Pirminis\Maybe;

// 1. simple value
$maybeInteger = new Maybe(123);
var_dump($maybeInteger->val());
// result: int(123)

// 2. accessing array elements
$maybeArray = new Maybe(['person' => ['name' => 'John']]);
var_dump($maybeArray['person']['name']->val());
// result: string(4) "John"
var_dump($maybeArray['oh, oh.']['what']->val('nope'));
// result: string(4) "nope"

// 3. simple value that is null
$maybeInteger = new Maybe(null);
var_dump($maybeInteger->val(0));
// result: int(0)


// 4. object
$obj = new stdClass();
$obj->title = 'Me so horny';

$maybeObj = new Maybe($obj);
var_dump($maybeObj->val());
// result:
// class stdClass#2 (1) {
//   public $title =>
//   string(11) "Me so horny"
// }


// 5. existing property
$obj = new stdClass();
$obj->age = 28;

$maybeObj = new Maybe($obj);
var_dump($maybeObj->age->val(0));
// result: int(28)


// 6. inexisting property
$obj = new stdClass();
$maybeObj = new Maybe($obj);

var_dump($maybeObj->name->val('property does not exist'));
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
$maybeOrder = new Maybe($order);

var_dump($maybeOrder->getUser()->getName()->val('no value'));
// result: string(4) "John"


// 8. chaining non existing methods
$maybeOrder = new Maybe(null);

var_dump($maybeOrder->getUser()->getName()->val('no value'));
// result: string(8) "no value"


// 9. using "empty" instead of "isset"
$maybeNotEmptyString = new Maybe('');

var_dump($maybeNotEmptyString->val('empty'));
// result: string(0) ""
var_dump($maybeNotEmptyString->val('empty', true));
// result: string(3) "empty"
