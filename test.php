<?php

require('./lib/Pirminis/Maybe.php');

use Pirminis\Maybe;

// 1. simple value
$maybeInteger = new Maybe(123);
var_dump($maybeInteger->value());
// result: int(123)


// 2. simple value that is null
$maybeInteger = new Maybe(null);
var_dump($maybeInteger->value(0));
// result: int(0)


// 3. object
$obj = new stdClass();
$obj->title = 'Me so horny';

$maybeObj = new Maybe($obj);
var_dump($maybeObj->value());
// result:
// class stdClass#2 (1) {
//   public $title =>
//   string(11) "Me so horny"
// }


// 4. existing property
$obj = new stdClass();
$obj->age = 28;

$maybeObj = new Maybe($obj);
var_dump($maybeObj->age->value(0));
// result: int(28)


// 5. inexisting property
$obj = new stdClass();
$maybeObj = new Maybe($obj);

var_dump($maybeObj->name->value('property does not exist'));
// result: string(23) "property does not exist"


// 6. chaining existing methods
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

var_dump($maybeOrder->getUser()->getName()->value('no value'));
// result: string(4) "John"


// 7. chaining non existing methods
$maybeOrder = new Maybe(null);

var_dump($maybeOrder->getUser()->getName()->value('no value'));
// result: string(8) "no value"