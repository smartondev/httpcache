<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Helpers\TimeHelper;
use SmartonDev\HttpCache\Exceptions\DateMalformedStringException;

it('duration to seconds', function () {
    expect(TimeHelper::durationToSeconds(37))
        ->toBe(expected: 37, message: '37 seconds')
        ->and(TimeHelper::durationToSeconds(hours: 1))
        ->toBe(expected: 3600, message: '1 hour')
        ->and(TimeHelper::durationToSeconds(minutes: 30))
        ->toBe(expected: 1800, message: '30 minutes')
        ->and(TimeHelper::durationToSeconds(seconds: 60))
        ->toBe(expected: 60, message: '60 seconds')
        ->and(TimeHelper::durationToSeconds(days: 1))
        ->toBe(expected: 86400, message: '1 day')
        ->and(TimeHelper::durationToSeconds(weeks: 1))
        ->toBe(expected: 604800, message: '1 week')
        ->and(TimeHelper::durationToSeconds(months: 1))
        ->toBe(expected: 2592000, message: '1 month')
        ->and(TimeHelper::durationToSeconds(years: 1))
        ->toBe(expected: 31536000, message: '1 year')
        ->and(TimeHelper::durationToSeconds(seconds: 2, minutes: 2, hours: 1))
        ->toBe(expected: 3722, message: '1 hour, 2 minutes, 2 seconds');
});

it('to timestamp', function (int|DateTime|string $input, int $expected) {
    expect(TimeHelper::toTimestamp($input))->toBe($expected);
})->with([
    'zero' => [0, 0],
    '2021-10-12 08:00' => ['2021-10-12T08:00:00Z', 1634025600],
    '2021-10-15 08:00' => ['2027-01-15T08:00:00Z', 1800000000],
    '2038-01-19 03:14:07' => ['2038-01-19T03:14:07Z', 2147483647],
    'date time object' => [new DateTime('2021-10-12T08:00:00Z'), 1634025600]
]);

it('to timestamp malformed string', function (string $input, string $expectedExceptionClass, string $expectedExceptionMessage) {
    expect(fn() => TimeHelper::toTimestamp($input))->toThrow(
        exception: $expectedExceptionClass,
        exceptionMessage: $expectedExceptionMessage
    );
})->with([
    'malformed string' => ['malformed string', DateMalformedStringException::class, 'Malformed date string'],
    'empty' => ['', \InvalidArgumentException::class, 'Date string is empty'],
    'blank' => ['  ', \InvalidArgumentException::class, 'Date string is empty']
]);
