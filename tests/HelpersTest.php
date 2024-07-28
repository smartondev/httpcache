<?php

namespace SmartonDev\HttpCache\Tests2;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function SmartonDev\HttpCache\durationToSeconds;
use function SmartonDev\HttpCache\getHeaderFirstValue;
use function SmartonDev\HttpCache\httpHeaderDate;
use function SmartonDev\HttpCache\isValidHttpHeaderDate;

class HelpersTest extends TestCase
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
        $this->assertSame($expectedValue, getHeaderFirstValue($headers, $header));
    }

    public function testDurationToSeconds(): void
    {
        $this->assertSame(37, durationToSeconds(37));
        $this->assertSame(3600, durationToSeconds(hours: 1));
        $this->assertSame(1800, durationToSeconds(minutes: 30));
        $this->assertSame(60, durationToSeconds(seconds: 60));
        $this->assertSame(86400, durationToSeconds(days: 1));
        $this->assertSame(604800, durationToSeconds(weeks: 1));
        $this->assertSame(2592000, durationToSeconds(months: 1));
        $this->assertSame(31536000, durationToSeconds(years: 1));
        $this->assertSame(3722, durationToSeconds(seconds: 2, minutes: 2, hours: 1));
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
        $this->assertSame($expectedDateString, httpHeaderDate($timestamp));
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
        $this->assertSame($expected, isValidHttpHeaderDate($value));
    }

}