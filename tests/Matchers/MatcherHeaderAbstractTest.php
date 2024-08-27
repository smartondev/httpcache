<?php

namespace SmartonDev\HttpCache\Tests\Matchers;

use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\Tests\Matchers\Mocks\MatcherHeaderAbstractMock;

class MatcherHeaderAbstractTest extends TestCase
{
    public function testHeaders(): void
    {
        $matcher = new MatcherHeaderAbstractMock();
        $matcher->headers(['header' => 'value']);
        $this->assertSame(['header' => 'value'], $matcher->getHeaders());
    }

    public function testWithHeaders(): void
    {
        $matcher = new MatcherHeaderAbstractMock();
        $matcher2 = $matcher->withHeaders(['header' => 'value']);
        $this->assertSame([], $matcher->getHeaders());
        $this->assertSame(['header' => 'value'], $matcher2->getHeaders());
    }

    public function testResetHeaders(): void
    {
        $matcher = new MatcherHeaderAbstractMock();
        $matcher->headers(['header' => 'value']);
        $matcher->resetHeaders();
        $this->assertSame([], $matcher->getHeaders());
    }

    public function testWithoutHeaders(): void
    {
        $matcher = new MatcherHeaderAbstractMock();
        $matcher->headers(['header' => 'value']);
        $matcher2 = $matcher->withoutHeaders();
        $this->assertSame(['header' => 'value'], $matcher->getHeaders());
        $this->assertSame([], $matcher2->getHeaders());
    }
}