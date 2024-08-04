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
                ->withIsWeekEtag()
                ->toHeaders()
        );
        $this->assertSame($expectedHeaders, $builder->toHeaders());
    }

    public static function dataProviderWithComputedEtag()
    {
        return [
            ['content123', 'md5', false, ['etag' => '"' . md5('content123') . '"']],
            ['content456', 'md5', true, ['etag' => 'W/"' . md5('content456') . '"']],
            ['content789', 'sha1', false, ['etag' => '"' . sha1('content789') . '"']],
            ['contentABC', 'sha1', true, ['etag' => 'W/"' . sha1('contentABC') . '"']],
            ['contentABCDE', 'strval', false, ['etag' => '"contentABCDE"']],
            ['contentABCDEFG', 'strval', true, ['etag' => 'W/"contentABCDEFG"']],
            [2, fn($d) => $d * 11, false, ['etag' => '"22"']],
            [35, fn($d) => $d / 5, true, ['etag' => 'W/"7"']],
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