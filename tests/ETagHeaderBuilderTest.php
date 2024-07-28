<?php

namespace SmartonDev\HttpCache\Tests2;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\ETagHeaderBuilder;

class ETagHeaderBuilderTest extends TestCase
{
    public static function dataProviderWithEtag(): array
    {
        return [
            ['123456', ['ETag' => '"123456"'], ['ETag' => 'W/"123456"']],
            ['abcABC123456', ['ETag' => '"abcABC123456"'], ['ETag' => 'W/"abcABC123456"']],
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
                ->withIsWeekEtag()
                ->toHeaders()
        );
        $this->assertSame($expectedHeaders, $builder->toHeaders());
    }

    public static function dataProviderWithComputedEtag()
    {
        return [
            ['content123', 'md5', false, ['ETag' => '"' . md5('content123') . '"']],
            ['content456', 'md5', true, ['ETag' => 'W/"' . md5('content456') . '"']],
            ['content789', 'sha1', false, ['ETag' => '"' . sha1('content789') . '"']],
            ['contentABC', 'sha1', true, ['ETag' => 'W/"' . sha1('contentABC') . '"']],
            ['contentABCDE', 'strval', false, ['ETag' => '"contentABCDE"']],
            ['contentABCDEFG', 'strval', true, ['ETag' => 'W/"contentABCDEFG"']],
            [2, fn($d) => $d * 11, false, ['ETag' => '"22"']],
            [35, fn($d) => $d / 5, true, ['ETag' => 'W/"7"']],
        ];
    }

    #[DataProvider('dataProviderWithComputedEtag')]
    public function testWithComputedEtag(mixed $data, callable $func, bool $weekEtag, array $expectedHeaders): void
    {
        $builder = (new ETagHeaderBuilder())
            ->withComputedEtag($data, $func);
        if ($weekEtag) {
            $builder = $builder->withIsWeekEtag();
        }
        $this->assertSame($expectedHeaders, $builder->toHeaders());
    }
}