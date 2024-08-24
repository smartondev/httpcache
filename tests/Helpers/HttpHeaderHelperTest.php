<?php

namespace SmartonDev\HttpCache\Tests\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;
use SmartonDev\HttpCache\Helpers\TimeHelper;

class HttpHeaderHelperTest extends TestCase
{

    public static function dataProviderGetHeaderFirstValue(): array
    {
        return [
            [['ETag' => '"123456"'], 'ETag', '"123456"'],
            [['ETag' => '"abcABC123456"'], 'ETag', '"abcABC123456"'],
            [[], 'Any', null],
            [['a' => 'b'], 'b', null],
            [['lowercase' => 'CamelCase'], 'LOWERCASE', 'CamelCase'],
            [['multiple-value' => ['first', 'second']], 'multiple-value', 'first'],
        ];
    }

    #[DataProvider('dataProviderGetHeaderFirstValue')]
    public function testGetHeaderFirstValue(array $headers, string $header, ?string $expectedValue): void
    {
        $this->assertSame($expectedValue, HttpHeaderHelper::getFirstHeaderValue($headers, $header));
    }

    public static function dataProviderHttpHeaderDate(): array
    {
        return [
            [0, 'Thu, 01 Jan 1970 00:00:00 GMT'],
            [1634025600, 'Tue, 12 Oct 2021 08:00:00 GMT'],
            [1800000000, 'Fri, 15 Jan 2027 08:00:00 GMT'],
            [2147483647, 'Tue, 19 Jan 2038 03:14:07 GMT'],
        ];
    }

    #[DataProvider('dataProviderHttpHeaderDate')]
    public function testHttpHeaderDate(int $timestamp, string $expectedDateString): void
    {
        $this->assertSame($expectedDateString, HttpHeaderHelper::toDateString($timestamp));
    }

    public static function dataProviderIsValidHttpHeaderDate(): array
    {
        return [
            ['', false],
            ['apple', false],
            ['Tue, 19 Jan 2038 03:14:07 GMT', true],
            ['Mon, 19 Jan 2038 03:14:07 GMT', false],
        ];
    }

    #[DataProvider('dataProviderIsValidHttpHeaderDate')]
    public function testIsValidHttpHeaderDate(string $value, bool $expected): void
    {
        $this->assertSame($expected, HttpHeaderHelper::isValidDateString($value));
    }

    public static function dataProviderReplaceHeaders(): array
    {
        return [
            'emptyInput replace' => [
                [],
                ['content-type' => 'application/json'],
                ['content-type' => 'application/json'],
            ],
            'emptyInput, replace uppercase' => [
                [],
                ['Content-Type' => 'application/json'],
                ['content-type' => 'application/json'],
            ],
            'uppercase input, replace lowercase' => [
                ['Content-Type' => 'text/plain'],
                ['content-type' => 'application/json'],
                ['content-type' => 'application/json'],
            ],
            'uppercase input with more, replace lowercase' => [
                ['Content-Type' => 'text/plain', 'x-no-change' => '1234'],
                ['content-type' => 'application/json'],
                ['content-type' => 'application/json', 'x-no-change' => '1234'],
            ],
        ];
    }

    #[DataProvider('dataProviderReplaceHeaders')]
    public function testReplaceHeaders(array $headers, array $replaceHeaders, array $expectedHeaders): void
    {
        $this->assertSame($expectedHeaders, HttpHeaderHelper::replaceHeaders($headers, $replaceHeaders));
    }

}