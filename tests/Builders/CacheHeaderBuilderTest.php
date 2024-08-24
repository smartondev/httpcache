<?php

namespace SmartonDev\HttpCache\Tests\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\Builders\CacheHeaderBuilderBuilder;
use SmartonDev\HttpCache\Builders\ETagHeaderBuilderBuilder;

class CacheHeaderBuilderTest extends TestCase
{
    public function testNoCache(): void
    {
        $noCacheExpectedHeaders = [
            'cache-control' => 'must-revalidate, no-cache, no-store, private',
            'pragma' => 'no-cache',
        ];
        $builder = (new CacheHeaderBuilderBuilder())
            ->withNoCache();
        $this->assertSame($noCacheExpectedHeaders, $builder->toHeaders());
        $builder = $builder->withSharedMaxAge(10);
        $this->assertNotEquals($noCacheExpectedHeaders, $builder->toHeaders());
        $this->assertFalse(strpos($builder->toHeaders()['cache-control'], 'no-cache'));
        $this->assertSame($noCacheExpectedHeaders, $builder->withNoCache()->toHeaders());
    }

    public function testMaxAgeWithDurations(): void
    {
        $builder = new CacheHeaderBuilderBuilder();
        $this->assertSame(['cache-control' => 'max-age=37'], $builder->withMaxAge(37)->toHeaders());
        $this->assertSame(['cache-control' => 'max-age=3600'], $builder->withMaxAge(hours: 1)->toHeaders());
        $this->assertSame(['cache-control' => 'max-age=1800'], $builder->withMaxAge(minutes: 30)->toHeaders());
        $this->assertSame(['cache-control' => 'max-age=60'], $builder->withMaxAge(seconds: 60)->toHeaders());
        $this->assertSame(['cache-control' => 'max-age=86400'], $builder->withMaxAge(days: 1)->toHeaders());
        $this->assertSame(['cache-control' => 'max-age=604800'], $builder->withMaxAge(weeks: 1)->toHeaders());
        $this->assertSame(['cache-control' => 'max-age=2592000'], $builder->withMaxAge(months: 1)->toHeaders());
        $this->assertSame(['cache-control' => 'max-age=31536000'], $builder->withMaxAge(years: 1)->toHeaders());
        $this->assertSame(['cache-control' => 'max-age=3722'], $builder->withMaxAge(seconds: 2, minutes: 2, hours: 1)->toHeaders());
    }

    public function testMaxAge(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withMaxAge(hours: 1);
        $this->assertSame(['cache-control' => 'max-age=3600'], $builder->toHeaders());
        $this->assertSame(
            ['cache-control' => 'max-age=1800'],
            $builder->withMaxAge(1800)
                ->toHeaders()
        );
    }

    public function testMaxAgeWithNoStore(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withMaxAge(3600)
            ->withNoStore();
        $this->assertSame(['cache-control' => 'max-age=3600, no-store'], $builder->toHeaders());
    }

    public function testMaxAgeWithAge(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withMaxAge(3600)
            ->withAge(1800);
        $this->assertSame([
            'cache-control' => 'max-age=3600',
            'age' => '1800',
        ], $builder->toHeaders());
    }

    public function testSharedMaxAge(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withSharedMaxAge(hours: 1);
        $this->assertSame(['cache-control' => 's-maxage=3600'], $builder->toHeaders());
        $this->assertSame(
            ['cache-control' => 's-maxage=1800'],
            $builder->withSharedMaxAge(minutes: 30)
                ->toHeaders()
        );
    }

    public function testNoStore(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withNoStore();
        $this->assertSame(['cache-control' => 'no-store'], $builder->toHeaders());
    }

    public function testPrivate(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withPrivate();
        $this->assertSame(['cache-control' => 'private'], $builder->toHeaders());
    }

    public function testPublic(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withPublic();
        $this->assertSame(['cache-control' => 'public'], $builder->toHeaders());
    }

    public function testMustRevalidate(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withMustRevalidate();
        $this->assertSame(['cache-control' => 'must-revalidate'], $builder->toHeaders());
    }

    public function testProxyRevalidate(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withProxyRevalidate();
        $this->assertSame(['cache-control' => 'proxy-revalidate'], $builder->toHeaders());
    }

    public function testMustUnderstand(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withMustUnderstand();
        $this->assertSame(['cache-control' => 'must-understand'], $builder->toHeaders());
    }

    public function testImmutable(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withImmutable();
        $this->assertSame(['cache-control' => 'immutable'], $builder->toHeaders());
    }

    public function testNoTransform(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withNoTransform();
        $this->assertSame(['cache-control' => 'no-transform'], $builder->toHeaders());
    }

    public function testStaleWhileRevalidate(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withStaleWhileRevalidate(hours: 1);
        $this->assertSame(['cache-control' => 'stale-while-revalidate=3600'], $builder->toHeaders());
        $this->assertSame(
            ['cache-control' => 'stale-while-revalidate=1800'],
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
        $builder = new CacheHeaderBuilderBuilder();
        $this->assertSame(['expires' => $dt], $builder->withExpires(strtotime($dt))->toHeaders());
        $this->assertSame(['expires' => $dt], $builder->withExpires(new \DateTime($dt))->toHeaders());
        $this->assertSame(['expires' => $dt], $builder->withExpires($dt)->toHeaders());
    }

    public function testStaleIfError(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withStaleIfError(3600);
        $this->assertSame(['cache-control' => 'stale-if-error=3600'], $builder->toHeaders());
        $this->assertSame(
            ['cache-control' => 'stale-if-error=1800'],
            $builder->withStaleIfError(minutes: 30)
                ->toHeaders()
        );
    }

    public function testWithETag(): void
    {
        $etagBuilder = (new ETagHeaderBuilderBuilder())
            ->withETag('123456');
        $builder = (new CacheHeaderBuilderBuilder())
            ->withETag($etagBuilder);
        $headers = $builder->toHeaders();
        $this->assertSame(['etag' => '"123456"'], $headers);
    }

    public function testWithEmptyEtag(): void
    {
        $builder = (new CacheHeaderBuilderBuilder())
            ->withETag('');
        $this->assertNull($builder->getETag());
        $this->assertFalse($builder->hasETag());

        $builder = (new CacheHeaderBuilderBuilder())
            ->withETag('   ');
        $this->assertNull($builder->getETag());
        $this->assertFalse($builder->hasETag());
    }

    public function testHasLastModified(): void
    {
        $builder = new CacheHeaderBuilderBuilder();
        $this->assertFalse($builder->hasLastModified());
        $builder->lastModified(1);
        $this->assertTrue($builder->hasLastModified());
        $builder->resetLastModified();
        $this->assertFalse($builder->hasLastModified());
    }

    public function testIsEmpty(): void
    {
        $builder = new CacheHeaderBuilderBuilder();
        $this->assertTrue($builder->isEmpty());
        $builder->noCache();
        $this->assertFalse($builder->isEmpty());
        $builder->reset();
        $this->assertTrue($builder->isEmpty());
    }

    public function testIsNotEmpty(): void
    {
        $builder = new CacheHeaderBuilderBuilder();
        $this->assertFalse($builder->isNotEmpty());
        $builder->noCache();
        $this->assertTrue($builder->isNotEmpty());
        $builder->reset();
        $this->assertFalse($builder->isNotEmpty());
    }

    public function testNoCacheReset(): void
    {
        $builder = new CacheHeaderBuilderBuilder();
        $builder->noCache();
        $this->assertSame([], $builder->withReset()->toHeaders());
        $this->assertSame(['cache-control' => 'private'], $builder->withPrivate()->toHeaders());
        $this->assertSame(['cache-control' => 'public'], $builder->withPublic()->toHeaders());
        $this->assertSame(['cache-control' => 'no-store'], $builder->withNoStore()->toHeaders());
        $this->assertSame(['cache-control' => 'must-revalidate'], $builder->withMustRevalidate()->toHeaders());
    }

    public function testIsNoCache(): void
    {
        $builder = new CacheHeaderBuilderBuilder();
        $this->assertFalse($builder->isNoCache());
        $builder->noCache();
        $this->assertTrue($builder->isNoCache());
        $builder->reset();
        $this->assertFalse($builder->isNoCache());

        $builder->noCache();
        $builder->public();
        $this->assertFalse($builder->isNoCache());
    }

    public static function dataProviderReset(): array
    {
        $builder = new CacheHeaderBuilderBuilder();
        return [
            'noCache' => [$builder->withNoCache()],
            'private' => [$builder->withPrivate()],
            'public' => [$builder->withPublic()],
            'noStore' => [$builder->withNoStore()],
            'mustRevalidate' => [$builder->withMustRevalidate()],
            'proxyRevalidate' => [$builder->withProxyRevalidate()],
            'mustUnderstand' => [$builder->withMustUnderstand()],
            'immutable' => [$builder->withImmutable()],
            'noTransform' => [$builder->withNoTransform()],
            'staleWhileRevalidate' => [$builder->withStaleWhileRevalidate(3600)],
            'staleIfError' => [$builder->withStaleIfError(3600)],
            'expires' => [$builder->withExpires('Sun, 05 Sep 2021 00:00:00 GMT')],
            'etag' => [$builder->withETag((new ETagHeaderBuilderBuilder())->withETag('123456'))],
            'age' => [$builder->withAge(1)],
            'sharedMaxAge' => [$builder->withSharedMaxAge(3600)],
            'maxAge' => [$builder->withMaxAge(3600)],
            'lastModified' => [$builder->withLastModified(1)],
        ];
    }

    #[DataProvider('dataProviderReset')]
    public function testReset(CacheHeaderBuilderBuilder $builder): void
    {
        $builder->noCache();
        $this->assertNotSame([], $builder->toHeaders());
        $this->assertSame([], $builder->withReset()->toHeaders());
        $builder->reset();
        $this->assertSame([], $builder->toHeaders());
    }
}