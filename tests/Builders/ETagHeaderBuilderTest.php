<?php

namespace SmartonDev\HttpCache\Tests\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\Builders\ETagHeaderBuilder;

class ETagHeaderBuilderTest extends TestCase
{
    public static function dataProviderWithEtag(): array
    {
        return [
            ['123456', ['etag' => '"123456"'], ['etag' => 'W/"123456"']],
            ['abcABC123456', ['etag' => '"abcABC123456"'], ['etag' => 'W/"abcABC123456"']],
        ];
    }

    #[DataProvider('dataProviderWithEtag')]
    public function testWithETag(string $etag, array $expectedHeaders, array $expectedWeekHeaders): void
    {
        $builder = (new ETagHeaderBuilder())
            ->withETag($etag);
        $this->assertSame(
            $expectedWeekHeaders,
            $builder
                ->withWeekEtag()
                ->toHeaders()
        );
        $this->assertSame($expectedHeaders, $builder->toHeaders());
    }

    public static function dataProviderComputedEtag(): array
    {
        return [
            ['content123', 'md5', false, ['etag' => '"' . md5('content123') . '"']],
            ['content456', 'md5', true, ['etag' => 'W/"' . md5('content456') . '"']],
            ['content789', 'sha1', false, ['etag' => '"' . sha1('content789') . '"']],
            ['contentABC', 'sha1', true, ['etag' => 'W/"' . sha1('contentABC') . '"']],
            ['contentABCDE', 'strval', false, ['etag' => '"contentABCDE"']],
            ['contentABCDEFG', 'strval', true, ['etag' => 'W/"contentABCDEFG"']],
            [2, fn($d) => strval($d * 11), false, ['etag' => '"22"']],
            [35, fn($d) => strval($d / 5), true, ['etag' => 'W/"7"']],
        ];
    }

    #[DataProvider('dataProviderComputedEtag')]
    public function testComputedEtag(mixed $data, callable $func, bool $weekEtag, array $expectedHeaders): void
    {
        $builder = (new ETagHeaderBuilder())
            ->computedETag($data, $func);
        if ($weekEtag) {
            $builder->weekETag();
        }
        $this->assertSame($expectedHeaders, $builder->toHeaders());
    }

    #[DataProvider('dataProviderComputedEtag')]
    public function testWithComputedEtag(mixed $data, callable $func, bool $weekEtag, array $expectedHeaders): void
    {
        $builder = (new ETagHeaderBuilder())
            ->withComputedETag($data, $func);
        if ($weekEtag) {
            $builder = $builder->withWeekETag();
        }
        $this->assertSame($expectedHeaders, $builder->toHeaders());
    }

    public function testEmptyETag(): void
    {
        $builder = (new ETagHeaderBuilder())
            ->etag('');
        $this->assertNull($builder->getETag());

        $builder = (new ETagHeaderBuilder())
            ->etag('    ');
        $this->assertNull($builder->getETag());
    }

    public function testIsNotEmptyETag(): void
    {
        $builder = (new ETagHeaderBuilder())
            ->etag('123456');
        $this->assertTrue($builder->isNotEmpty());

        $builder = (new ETagHeaderBuilder())
            ->etag('    ');
        $this->assertFalse($builder->isNotEmpty());
    }

    public function testResetETag(): void
    {
        $builder = (new ETagHeaderBuilder())
            ->etag('123456');
        $this->assertTrue($builder->isNotEmpty());
        $this->assertFalse($builder->withoutETag()->isNotEmpty());
        $this->assertTrue($builder->isNotEmpty());
        $builder->resetETag();
        $this->assertFalse($builder->isNotEmpty());
    }

    public function testResetWeekETag(): void
    {
        $builder = (new ETagHeaderBuilder())
            ->etag('123456')
            ->weekETag();
        $this->assertSame('W/"123456"', $builder->getETag());
        $this->assertSame('"123456"', $builder->withoutWeekETag()->getETag());
        $this->assertSame('W/"123456"', $builder->getETag());
        $builder->resetWeekETag();
        $this->assertSame('"123456"', $builder->getETag());
    }

    public function testEmpty(): void
    {
        $builder = new ETagHeaderBuilder();
        $this->assertSame([], $builder->toHeaders());
    }

    public function testToString(): void
    {
        $builder = (new ETagHeaderBuilder())
            ->etag('123456');
        $this->assertSame('"123456"', (string)$builder);
        $this->assertSame($builder->getETag(), (string)$builder);

        $builder->weekETag();
        $this->assertSame('W/"123456"', (string)$builder);
        $this->assertSame($builder->getETag(), (string)$builder);
    }
}