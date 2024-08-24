<?php

namespace SmartonDev\HttpCache\Tests\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;
use SmartonDev\HttpCache\Builders\ETagHeaderBuilder;

class CacheHeaderBuilderTest extends TestCase
{
    public function testNoCache(): void
    {
        $noCacheExpectedHeaders = [
            'cache-control' => 'must-revalidate, no-cache, no-store, private',
            'pragma' => 'no-cache',
        ];
        $builder = (new CacheHeaderBuilder())
            ->withNoCache();
        $this->assertSame($noCacheExpectedHeaders, $builder->toHeaders());
        $builder = $builder->withSharedMaxAge(10);
        $this->assertNotEquals($noCacheExpectedHeaders, $builder->toHeaders());
        $this->assertFalse(strpos($builder->toHeaders()['cache-control'], 'no-cache'));
        $this->assertSame($noCacheExpectedHeaders, $builder->withNoCache()->toHeaders());
    }

    public static function dataProviderMaxAgeWithDurations(): array
    {
        $builder = new CacheHeaderBuilder();
        return [
            '37' => [['cache-control' => 'max-age=37'], $builder->withMaxAge(37)],
            'hours:1' => [['cache-control' => 'max-age=3600'], $builder->withMaxAge(hours: 1)],
            'minutes:30' => [['cache-control' => 'max-age=1800'], $builder->withMaxAge(minutes: 30)],
            'seconds:60' => [['cache-control' => 'max-age=60'], $builder->withMaxAge(seconds: 60)],
            'days:1' => [['cache-control' => 'max-age=86400'], $builder->withMaxAge(days: 1)],
            'weeks:1' => [['cache-control' => 'max-age=604800'], $builder->withMaxAge(weeks: 1)],
            'months:1' => [['cache-control' => 'max-age=2592000'], $builder->withMaxAge(months: 1)],
            'years:1' => [['cache-control' => 'max-age=31536000'], $builder->withMaxAge(years: 1)],
            'seconds:2, minutes:2, hours:1' => [['cache-control' => 'max-age=3722'], $builder->withMaxAge(seconds: 2, minutes: 2, hours: 1)],
        ];
    }

    #[DataProvider('dataProviderMaxAgeWithDurations')]
    public function testMaxAgeWithDurations(array $expectedHeaders, CacheHeaderBuilder $builder): void
    {
        $this->assertSame($expectedHeaders, $builder->toHeaders());
    }

    public function testMaxAge(): void
    {
        $builder = (new CacheHeaderBuilder())
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
        $builder = (new CacheHeaderBuilder())
            ->withMaxAge(3600)
            ->withNoStore();
        $this->assertSame(['cache-control' => 'max-age=3600, no-store'], $builder->toHeaders());
    }

    public function testMaxAgeWithAge(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withMaxAge(3600)
            ->withAge(1800);
        $this->assertSame([
            'cache-control' => 'max-age=3600',
            'age' => '1800',
        ], $builder->toHeaders());
    }

    public function testSharedMaxAge(): void
    {
        $builder = (new CacheHeaderBuilder())
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
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['cache-control' => 'no-store'], $builder->withNoStore()->toHeaders());
        $this->assertSame([], $builder->toHeaders());
        $this->assertSame(['cache-control' => 'no-store'], $builder->noStore()->toHeaders());
        $this->assertSame(['cache-control' => 'no-store'], $builder->toHeaders());
    }

    public function testPrivate(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['cache-control' => 'private'], $builder->withPrivate()->toHeaders());
        $this->assertSame([], $builder->toHeaders());
        $this->assertSame(['cache-control' => 'private'], $builder->private()->toHeaders());
        $this->assertSame(['cache-control' => 'private'], $builder->toHeaders());
    }

    public function testPublic(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['cache-control' => 'public'], $builder->withPublic()->toHeaders());
        $this->assertSame([], $builder->toHeaders());
        $this->assertSame(['cache-control' => 'public'], $builder->public()->toHeaders());
        $this->assertSame(['cache-control' => 'public'], $builder->toHeaders());
    }

    public function testMustRevalidate(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['cache-control' => 'must-revalidate'], $builder->withMustRevalidate()->toHeaders());
        $this->assertSame([], $builder->toHeaders());
        $this->assertSame(['cache-control' => 'must-revalidate'], $builder->mustRevalidate()->toHeaders());
        $this->assertSame(['cache-control' => 'must-revalidate'], $builder->toHeaders());
    }

    public function testProxyRevalidate(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['cache-control' => 'proxy-revalidate'], $builder->withProxyRevalidate()->toHeaders());
        $this->assertSame([], $builder->toHeaders());
        $this->assertSame(['cache-control' => 'proxy-revalidate'], $builder->proxyRevalidate()->toHeaders());
        $this->assertSame(['cache-control' => 'proxy-revalidate'], $builder->toHeaders());
    }

    public function testMustUnderstand(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['cache-control' => 'must-understand'], $builder->withMustUnderstand()->toHeaders());
        $this->assertSame([], $builder->toHeaders());
        $this->assertSame(['cache-control' => 'must-understand'], $builder->mustUnderstand()->toHeaders());
        $this->assertSame(['cache-control' => 'must-understand'], $builder->toHeaders());
    }

    public function testImmutable(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['cache-control' => 'immutable'], $builder->withImmutable()->toHeaders());
        $this->assertSame([], $builder->toHeaders());
        $this->assertSame(['cache-control' => 'immutable'], $builder->immutable()->toHeaders());
        $this->assertSame(['cache-control' => 'immutable'], $builder->toHeaders());
    }

    public function testNoTransform(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['cache-control' => 'no-transform'], $builder->withNoTransform()->toHeaders());
        $this->assertSame([], $builder->toHeaders());
        $this->assertSame(['cache-control' => 'no-transform'], $builder->noTransform()->toHeaders());
        $this->assertSame(['cache-control' => 'no-transform'], $builder->toHeaders());
    }

    public function testStaleWhileRevalidate(): void
    {
        $builder = (new CacheHeaderBuilder())
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
        $builder = new CacheHeaderBuilder();
        $this->assertSame(['expires' => $dt], $builder->withExpires(strtotime($dt))->toHeaders());
        $this->assertSame(['expires' => $dt], $builder->withExpires(new \DateTime($dt))->toHeaders());
        $this->assertSame(['expires' => $dt], $builder->withExpires($dt)->toHeaders());
    }

    public function testStaleIfError(): void
    {
        $builder = (new CacheHeaderBuilder())
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
        $etagBuilder = (new ETagHeaderBuilder())
            ->withETag('123456');
        $builder = (new CacheHeaderBuilder())
            ->withETag($etagBuilder);
        $headers = $builder->toHeaders();
        $this->assertSame(['etag' => '"123456"'], $headers);
    }

    public function testWithEmptyEtag(): void
    {
        $builder = (new CacheHeaderBuilder())
            ->withETag('');
        $this->assertNull($builder->getETag());
        $this->assertFalse($builder->hasETag());

        $builder = (new CacheHeaderBuilder())
            ->withETag('   ');
        $this->assertNull($builder->getETag());
        $this->assertFalse($builder->hasETag());
    }

    public function testHasLastModified(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertFalse($builder->hasLastModified());
        $builder->lastModified(1);
        $this->assertTrue($builder->hasLastModified());
        $builder->resetLastModified();
        $this->assertFalse($builder->hasLastModified());
    }

    public function testIsEmpty(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertTrue($builder->isEmpty());
        $builder->noCache();
        $this->assertFalse($builder->isEmpty());
        $builder->reset();
        $this->assertTrue($builder->isEmpty());
    }

    public function testIsNotEmpty(): void
    {
        $builder = new CacheHeaderBuilder();
        $this->assertFalse($builder->isNotEmpty());
        $builder->noCache();
        $this->assertTrue($builder->isNotEmpty());
        $builder->reset();
        $this->assertFalse($builder->isNotEmpty());
    }

    public function testNoCacheReset(): void
    {
        $builder = new CacheHeaderBuilder();
        $builder->noCache();
        $this->assertSame([], $builder->withReset()->toHeaders());
        $this->assertSame(['cache-control' => 'private'], $builder->withPrivate()->toHeaders());
        $this->assertSame(['cache-control' => 'public'], $builder->withPublic()->toHeaders());
        $this->assertSame(['cache-control' => 'no-store'], $builder->withNoStore()->toHeaders());
        $this->assertSame(['cache-control' => 'must-revalidate'], $builder->withMustRevalidate()->toHeaders());
    }

    public function testIsNoCache(): void
    {
        $builder = new CacheHeaderBuilder();
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
        $builder = new CacheHeaderBuilder();
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
            'etag' => [$builder->withETag((new ETagHeaderBuilder())->withETag('123456'))],
            'age' => [$builder->withAge(1)],
            'sharedMaxAge' => [$builder->withSharedMaxAge(3600)],
            'maxAge' => [$builder->withMaxAge(3600)],
            'lastModified' => [$builder->withLastModified(1)],
        ];
    }

    #[DataProvider('dataProviderReset')]
    public function testReset(CacheHeaderBuilder $builder): void
    {
        $builder->noCache();
        $this->assertNotSame([], $builder->toHeaders());
        $this->assertSame([], $builder->withReset()->toHeaders());
        $builder->reset();
        $this->assertSame([], $builder->toHeaders());
    }
}