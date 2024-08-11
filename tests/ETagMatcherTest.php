<?php

namespace SmartonDev\HttpCache\Tests2;

use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\ETagMatcher;
use SmartonDev\HttpCache\ETagHeaderBuilder;

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
        $this->assertFalse(
            $ETagCondition
                ->matches(null)
                ->matchesIfMatchHeader()
        );
    }

    public function testIsNoneMatchWithNullEtag(): void
    {
        $ETagCondition = (new ETagMatcher())
            ->withHeaders(['If-None-Match' => '"123456"']);
        $this->assertFalse(
            $ETagCondition
                ->matches(null)
                ->matchesIfNoneMatchHeader()
        );
    }

    public function testIsNoneMatchFail(): void
    {
        $ETagCondition = (new ETagMatcher())
            ->withHeaders(['If-None-Match' => '"123456"']);
        $this->assertFalse(
            $ETagCondition
                ->matches('"1234567"')
                ->matchesIfNoneMatchHeader()
        );
    }

    public function testIsMatchFail(): void
    {
        $ETagCondition = (new ETagMatcher())
            ->withHeaders(['If-Match' => '"123456"']);
        $this->assertFalse(
            $ETagCondition
                ->matches('"1234567"')
                ->matchesIfMatchHeader()
        );
    }

    public function testMatchWithETagHeaderBuilder(): void
    {
        $ETagHeaderBuilder = (new ETagHeaderBuilder())
            ->withETag('123456')
            ->withWeekEtag();
        $etag = $ETagHeaderBuilder->getETag();
        $ETagCondition = new ETagMatcher();
        $this->assertTrue(
            $ETagCondition
                ->withHeaders(['If-Match' => $etag])
                ->matches($etag)
                ->matchesIfMatchHeader()
        );
        $this->assertTrue(
            $ETagCondition
                ->withHeaders(['If-None-Match' => $etag])
                ->matches($etag)
                ->matchesIfNoneMatchHeader()
        );
    }
}