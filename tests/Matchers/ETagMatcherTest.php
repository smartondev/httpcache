<?php

namespace SmartonDev\HttpCache\Tests\Matchers;

use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\Builders\ETagHeaderBuilder;
use SmartonDev\HttpCache\Matchers\ETagMatcher;

class ETagMatcherTest extends TestCase
{
    public function testIsMatch(): void
    {
        $ETagCondition = (new ETagMatcher())
            ->headers(['If-Match' => '"123456"']);
        $this->assertTrue(
            $ETagCondition
                ->matches('"123456"')
                ->matchesIfMatchHeader()
        );
    }

    public function testIsNoneMatch(): void
    {
        $ETagCondition = (new ETagMatcher())
            ->withHeaders(['If-None-Match' => '"123456"']);
        $this->assertTrue(
            $ETagCondition
                ->matches('"123456"')
                ->matchesIfNoneMatchHeader()
        );
    }

    public function testIsMatchWithNullEtag(): void
    {
        $ETagCondition = (new ETagMatcher())
            ->withHeaders(['If-Match' => '"123456"']);
        $matches = $ETagCondition->matches(null);
        $this->assertFalse($matches->matchesIfMatchHeader());
        $this->assertTrue($matches->notMatchesIfMatchHeader());
    }

    public function testIsNoneMatchWithNullEtag(): void
    {
        $ETagCondition = (new ETagMatcher())
            ->withHeaders(['If-None-Match' => '"123456"']);
        $matches = $ETagCondition->matches(null);
        $this->assertFalse($matches->matchesIfNoneMatchHeader());
        $this->assertTrue($matches->notMatchesIfNoneMatchHeader());
    }

    public function testIsNoneMatchFail(): void
    {
        $ETagCondition = (new ETagMatcher())
            ->withHeaders(['If-None-Match' => '"123456"']);
        $matches = $ETagCondition->matches('"1234567"');
        $this->assertFalse($matches->matchesIfNoneMatchHeader());
        $this->assertTrue($matches->notMatchesIfNoneMatchHeader());
    }

    public function testIsMatchFail(): void
    {
        $ETagCondition = (new ETagMatcher())
            ->withHeaders(['If-Match' => '"123456"']);
        $matches = $ETagCondition->matches('"1234567"');
        $this->assertFalse($matches->matchesIfMatchHeader());
        $this->assertTrue($matches->notMatchesIfMatchHeader());
    }

    public function testMatchWithETagHeaderBuilder(): void
    {
        $ETagHeaderBuilder = (new ETagHeaderBuilder())
            ->withETag('123456')
            ->withWeekEtag();
        $etag = $ETagHeaderBuilder->getETag();
        $ETagCondition = new ETagMatcher();
        $matchesIfMatch = $ETagCondition
            ->withHeaders(['If-Match' => $etag])
            ->matches($etag);
        $this->assertTrue($matchesIfMatch->matchesIfMatchHeader());
        $this->assertFalse($matchesIfMatch->notMatchesIfMatchHeader());

        $matchesIfNoneMatch = $ETagCondition
            ->withHeaders(['If-None-Match' => $etag])
            ->matches($etag);
        $this->assertTrue($matchesIfNoneMatch->matchesIfNoneMatchHeader());
        $this->assertFalse($matchesIfNoneMatch->notMatchesIfNoneMatchHeader());
    }

    public function testIfMatchHeader(): void
    {
        $matcher = new ETagMatcher();
        $matcher2 = $matcher->withIfMatchHeader('"123"');
        $this->assertFalse($matcher->hasIfMatchHeader());
        $this->assertTrue($matcher2->hasIfMatchHeader());
        $this->assertSame('"123"', $matcher2->getIfMatchHeader());
        $matcher->ifMatchHeader('"456"');
        $this->assertTrue($matcher->hasIfMatchHeader());
        $this->assertSame('"456"', $matcher->getIfMatchHeader());
    }

    public function testIfNoneMatchHeader(): void
    {
        $matcher = new ETagMatcher();
        $matcher2 = $matcher->withIfNoneMatchHeaderValue('"123"');
        $this->assertFalse($matcher->hasIfNoneMatchHeader());
        $this->assertTrue($matcher2->hasIfNoneMatchHeader());
        $this->assertSame('"123"', $matcher2->getIfNoneMatchHeader());
        $matcher->ifNoneMatchHeaderValue('"456"');
        $this->assertTrue($matcher->hasIfNoneMatchHeader());
        $this->assertSame('"456"', $matcher->getIfNoneMatchHeader());
    }
}