<?php

namespace Pirminis;

final class None extends Maybe
{
    public function is_some()
    {
        return false;
    }

    public function is_none()
    {
        return true;
    }
}
