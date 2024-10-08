<?php

namespace SmartonDev\HttpCache\Tests\Matchers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;

class ModifiedMatcherTest extends TestCase
{

    public static function dataProviderHeaderExists(): array
    {
        return [
            [[], false, false],
            [['If-Modified-Since' => ''], true, false],
            [['If-Unmodified-Since' => ''], false, true],
            [['If-Modified-Since' => '', 'If-Unmodified-Since' => ''], true, true],
        ];
    }

    #[DataProvider('dataProviderHeaderExists')]
    public function testHeaderExists(array $headers, bool $expectedExistsIfModifiedSince, bool $expectedExistsIfUnmodifiedSince): void
    {
        $matcher = (new ModifiedMatcher())->headers($headers);

        $this->assertSame($expectedExistsIfModifiedSince, $matcher->hasIfModifiedSinceHeader());
        $this->assertSame($expectedExistsIfUnmodifiedSince, $matcher->hasIfUnmodifiedSinceHeader());
    }

    public static function dataProviderHeaderIsValid(): array
    {
        return [
            [[], false, false],
            [['If-Modified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT'], true, false],
            [['If-Modified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT', 'If-Unmodified-Since' => 'Tue, 15 Nov 1994 12:45:27 GMT'], true, true],
            [['If-Modified-Since' => 'apple', 'If-Unmodified-Since' => 'Tue, 15 Nov 1994 12:45:27 GMT'], false, true],
            [['If-Modified-Since' => 'apple', 'If-Unmodified-Since' => 'peach'], false, false],
        ];
    }

    #[DataProvider('dataProviderHeaderIsValid')]
    public function testHeaderIsValid(array $headers, bool $expectedIfModifiedSince, bool $expectedIfUnmodifiedSince): void
    {
        $matcher = (new ModifiedMatcher())->headers($headers);

        $this->assertSame($expectedIfModifiedSince, $matcher->isValidIfModifiedSinceHeader());
        $this->assertSame(!$expectedIfModifiedSince, $matcher->isInvalidIfModifiedSinceHeader());
        $this->assertSame($expectedIfUnmodifiedSince, $matcher->isValidIfUnmodifiedSinceHeader());
        $this->assertSame(!$expectedIfUnmodifiedSince, $matcher->isInvalidIfUnmodifiedSinceHeader());
    }

    public static function dataProviderHeaderAsTimestamp(): array
    {
        return [
            [[], null, null],
            [['If-Modified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT'], 784903526, null],
            [['If-Modified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT', 'If-Unmodified-Since' => 'Tue, 15 Nov 1994 12:45:27 GMT'], 784903526, 784903527],
        ];
    }

    #[DataProvider('dataProviderHeaderAsTimestamp')]
    public function testHeaderAsTimestamp(array $headers,
                                          ?int  $expectedIfModifiedSinceAsTimestamp,
                                          ?int  $expectedIfUnmodifiedSinceAsTimestamp): void
    {
        $matcher = (new ModifiedMatcher())->headers($headers);
        $this->assertSame($expectedIfModifiedSinceAsTimestamp, $matcher->getIfModifiedSinceHeaderAsTimestamp());
        $this->assertSame($expectedIfUnmodifiedSinceAsTimestamp, $matcher->getIfUnmodifiedSinceHeaderAsTimestamp());
    }

    public function testGetModifiedSinceHeaderAsTimestampInvalidValue(): void
    {
        $matcher = (new ModifiedMatcher())->headers([
            'If-Modified-Since' => 'apple',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid If-Modified-Since header value');
        $matcher->getIfModifiedSinceHeaderAsTimestamp();
    }

    public function testGetUnmodifiedSinceHeaderAsTimestampInvalidValue(): void
    {
        $matcher = (new ModifiedMatcher())->headers([
            'If-Unmodified-Since' => 'apple',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid If-Unmodified-Since header value');
        $matcher->getIfUnmodifiedSinceHeaderAsTimestamp();
    }

    public function testMatchesIfModifiedSince(): void
    {
        $matcher = (new ModifiedMatcher())->headers([
            'If-Modified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT',
        ]);

        $dtBefore = new \DateTime('Mon, 14 Nov 1994 12:45:26 GMT');
        $dtAfter = new \DateTime('Wed, 16 Nov 1994 12:45:26 GMT');
        $dtEq = new \DateTime('Tue, 15 Nov 1994 12:45:26 GMT');
        $this->assertTrue($matcher->matches($dtEq)->matchesModifiedAt());
        $this->assertFalse($matcher->matches($dtBefore)->matchesModifiedAt());
        $this->assertTrue($matcher->matches($dtAfter)->isModifiedSince());
    }

    public function testMatchesIfUnmodifiedSince(): void
    {
        $matcher = (new ModifiedMatcher())->headers([
            'If-Unmodified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT',
        ]);

        $dtBefore = new \DateTime('Mon, 14 Nov 1994 12:45:26 GMT');
        $dtAfter = new \DateTime('Wed, 16 Nov 1994 12:45:26 GMT');
        $dtEq = new \DateTime('Tue, 15 Nov 1994 12:45:26 GMT');
        $this->assertTrue($matcher->matches($dtBefore)->isUnmodifiedSince());
        $this->assertFalse($matcher->matches($dtAfter)->isUnmodifiedSince());
        $this->assertFalse($matcher->matches($dtEq)->isModifiedSince());
    }

    public function testIfModifiedSinceHeader(): void
    {
        $matcher = new ModifiedMatcher();
        $matcher2 = $matcher->withIfModifiedSinceHeader('Tue, 15 Nov 1994 12:45:26 GMT');
        $this->assertFalse($matcher->hasIfModifiedSinceHeader());
        $this->assertTrue($matcher2->hasIfModifiedSinceHeader());
        $this->assertSame('Tue, 15 Nov 1994 12:45:26 GMT', $matcher2->getIfModifiedSinceHeader());
        $matcher->ifModifiedSinceHeader('Wed, 16 Nov 1994 12:45:26 GMT');
        $this->assertTrue($matcher->hasIfModifiedSinceHeader());
        $this->assertSame('Wed, 16 Nov 1994 12:45:26 GMT', $matcher->getIfModifiedSinceHeader());
    }

    public function testIfUnmodifiedSinceHeader(): void
    {
        $matcher = new ModifiedMatcher();
        $matcher2 = $matcher->withIfUnmodifiedSinceHeader('Tue, 15 Nov 1994 12:45:26 GMT');
        $this->assertFalse($matcher->hasIfUnmodifiedSinceHeader());
        $this->assertTrue($matcher2->hasIfUnmodifiedSinceHeader());
        $this->assertSame('Tue, 15 Nov 1994 12:45:26 GMT', $matcher2->getIfUnmodifiedSinceHeader());
        $matcher->ifUnmodifiedSinceHeader('Wed, 16 Nov 1994 12:45:26 GMT');
        $this->assertTrue($matcher->hasIfUnmodifiedSinceHeader());
        $this->assertSame('Wed, 16 Nov 1994 12:45:26 GMT', $matcher->getIfUnmodifiedSinceHeader());
    }
}