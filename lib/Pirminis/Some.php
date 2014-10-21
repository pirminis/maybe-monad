<?php

namespace Pirminis;

final class Some extends Maybe
{
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function is_some()
    {
        return true;
    }

    public function is_none()
    {
        return false;
    }
}
