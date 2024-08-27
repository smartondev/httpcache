<?php

namespace SmartonDev\HttpCache\Tests\Matchers\Mocks;

use SmartonDev\HttpCache\Matchers\MatcherHeaderAbstract;

class MatcherHeaderAbstractMock extends MatcherHeaderAbstract
{
    public function getHeaders() : array
    {
        return $this->headers;
    }
}