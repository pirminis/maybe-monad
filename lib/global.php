<?php

// global namespace. ain't this hack ugly?
namespace {

use Pirminis\Maybe;
use Pirminis\Some;
use Pirminis\None;

function Maybe($val = null)
{
    if ($val instanceof \Pirminis\Maybe) {
        return $val;
    } else {
        if (is_null($val)) return new None();
        else return new Some($val);
    }
}

}
