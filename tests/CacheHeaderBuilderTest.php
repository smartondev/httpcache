<?php

namespace SmartonDev\HttpCache\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\CacheHeaderBuilder;
use SmartonDev\HttpCache\ETagHeaderBuilder;

class CacheHeaderBuilderTest extends TestCase
{
    public function testNoCache(): void
    {
        $noCacheExpectedHeaders = [
            'Cache-Control' => 'must-revalidate, no-store, private, no-cache',
            'Pragma' => 'no-cache',
        ];
        $builder = (new CacheHeaderBuilder())
            ->withNoCache();
        $this->assertSame($noCacheExpectedHeaders, $builder->toHeaders());
        $builder = $builder->withSharedMaxAge(10);
        $this->assertNotEquals($noCacheExpectedHeaders, $builder->toHeaders());
        $this->assertFalse(strpos($builder->toHeaders()['Cache-Control'], 'no-cache'));
        $this->assertSame($noCacheExpectedHeaders, $builder->withNoCache()->toHeaders());
    }

    public function testMaxAgeWithDurations(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['Cache-Control' => 'max-age=37'], $builder->withMaxAge(37)->toHeaders());
        $this->assertSame(['Cache-Control' => 'max-age=3600'], $builder->withMaxAge(hours: 1)->toHeaders());
        $this->assertSame(['Cache-Control' => 'max-age=1800'], $builder->withMaxAge(minutes: 30)->toHeaders());
        $this->assertSame(['Cache-Control' => 'max-age=60'], $builder->withMaxAge(seconds: 60)->toHeaders());
        $this->assertSame(['Cache-Control' => 'max-age=86400'], $builder->withMaxAge(days: 1)->toHeaders());
        $this->assertSame(['Cache-Control' => 'max-age=604800'], $builder->withMaxAge(weeks: 1)->toHeaders());
        $this->assertSame(['Cache-Control' => 'max-age=2592000'], $builder->withMaxAge(months: 1)->toHeaders());
        $this->assertSame(['Cache-Control' => 'max-age=31536000'], $builder->withMaxAge(years: 1)->toHeaders());
        $this->assertSame(['Cache-Control' => 'max-age=3722'], $builder->withMaxAge(seconds: 2, minutes: 2, hours: 1)->toHeaders());
    }

    public function testMaxAge(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withMaxAge(hours: 1);
        $this->assertSame(['Cache-Control' => 'max-age=3600'], $builder->toHeaders());
        $this->assertSame(
            ['Cache-Control' => 'max-age=1800'],
            $builder->withMaxAge(1800)
                ->toHeaders()
        );
    }

    public function testMaxAgeWithNoStore(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withMaxAge(3600)
            ->withNoStore();
        $this->assertSame(['Cache-Control' => 'no-store, max-age=3600'], $builder->toHeaders());
    }

    public function testMaxAgeWithAge(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withMaxAge(3600)
            ->withAge(1800);
        $this->assertSame([
            'Cache-Control' => 'max-age=3600',
            'Age' => '1800',
        ], $builder->toHeaders());
    }

    public function testSharedMaxAge(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withSharedMaxAge(hours: 1);
        $this->assertSame(['Cache-Control' => 's-maxage=3600'], $builder->toHeaders());
        $this->assertSame(
            ['Cache-Control' => 's-maxage=1800'],
            $builder->withSharedMaxAge(minutes: 30)
                ->toHeaders()
        );
    }

    public function testNoStore(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withNoStore();
        $this->assertSame(['Cache-Control' => 'no-store'], $builder->toHeaders());
    }

    public function testPrivate(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withPrivate();
        $this->assertSame(['Cache-Control' => 'private'], $builder->toHeaders());
    }

    public function testPublic(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withPublic();
        $this->assertSame(['Cache-Control' => 'public'], $builder->toHeaders());
    }

    public function testMustRevalidate(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withMustRevalidate();
        $this->assertSame(['Cache-Control' => 'must-revalidate'], $builder->toHeaders());
    }

    public function testProxyRevalidate(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withProxyRevalidate();
        $this->assertSame(['Cache-Control' => 'proxy-revalidate'], $builder->toHeaders());
    }

    public function testMustUnderstand(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withMustUnderstand();
        $this->assertSame(['Cache-Control' => 'must-understand'], $builder->toHeaders());
    }

    public function testImmutable(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withImmutable();
        $this->assertSame(['Cache-Control' => 'immutable'], $builder->toHeaders());
    }

    public function testNoTransform(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withNoTransform();
        $this->assertSame(['Cache-Control' => 'no-transform'], $builder->toHeaders());
    }

    public function testStaleWhileRevalidate(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withStaleWhileRevalidate(hours: 1);
        $this->assertSame(['Cache-Control' => 'stale-while-revalidate=3600'], $builder->toHeaders());
        $this->assertSame(
            ['Cache-Control' => 'stale-while-revalidate=1800'],
            $builder->withStaleWhileRevalidate(minutes: 30)
                ->toHeaders()
        );
    }

    public static function dataProviderExpires(): array
    {
        return [
            ['Sun, 05 Sep 2021 00:00:00 GMT'],
            ['Mon, 06 Sep 2021 01:00:00 GMT'],
            ['Sun, 12 Sep 2021 01:02:03 GMT'],
        ];
    }

    #[DataProvider('dataProviderExpires')]
    public function testExpires(string $dt): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['Expires' => $dt], $builder->withExpires(strtotime($dt))->toHeaders());
        $this->assertSame(['Expires' => $dt], $builder->withExpires(new \DateTime($dt))->toHeaders());
        $this->assertSame(['Expires' => $dt], $builder->withExpires($dt)->toHeaders());
    }

    public function testStaleIfError(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withStaleIfError(3600);
        $this->assertSame(['Cache-Control' => 'stale-if-error=3600'], $builder->toHeaders());
        $this->assertSame(
            ['Cache-Control' => 'stale-if-error=1800'],
            $builder->withStaleIfError(minutes: 30)
                ->toHeaders()
        );
    }

    public function testWithETag(): void
    {
        $etagBuilder = (new ETagHeaderBuilder())
            ->withETag('123456');
        $builder = (new CacheHeaderBuilder())
            ->withETag($etagBuilder);
        $headers = $builder->toHeaders();
        $this->assertSame(['ETag' => '"123456"'], $headers);
    }
}