<?php

namespace Pirminis;

final class None extends Maybe
{
    public function __construct($subject = null)
    {
        $this->subject = null;
    }

    public function is_some()
    {
        return false;
    }

    public function is_none()
    {
        return true;
    }
}
