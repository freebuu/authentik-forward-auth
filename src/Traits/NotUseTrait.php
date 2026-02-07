<?php

namespace FreeBuu\ForwardAuth\Traits;

trait NotUseTrait
{
    private function mustNotUse(string $method)
    {
        throw new \LogicException(sprintf('Method %s::%s must not be used.', __CLASS__, $method));
    }
}
