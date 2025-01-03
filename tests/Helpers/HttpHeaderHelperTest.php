<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;

it('get first header value', function (array $headers, string $header, ?string $expected) {
    expect(HttpHeaderHelper::getFirstHeaderValue($headers, $header))->toBe($expected);
})->with([
    [['ETag' => '"123456"'], 'ETag', '"123456"'],
    [['ETag' => '"abcABC123456"'], 'ETag', '"abcABC123456"'],
    [[], 'Any', null],
    [['a' => 'b'], 'b', null],
    [['lowercase' => 'CamelCase'], 'LOWERCASE', 'CamelCase'],
    [['multiple-value' => ['first', 'second']], 'multiple-value', 'first'],
]);

it('to date string', function (int $timestamp, string $expectedDateString) {
    expect(HttpHeaderHelper::toDateString($timestamp))->toBe($expectedDateString);
})->with([
    [0, 'Thu, 01 Jan 1970 00:00:00 GMT'],
    [1634025600, 'Tue, 12 Oct 2021 08:00:00 GMT'],
    [1800000000, 'Fri, 15 Jan 2027 08:00:00 GMT'],
    [2147483647, 'Tue, 19 Jan 2038 03:14:07 GMT'],
]);

it('is valid date string', function (string $value, bool $expected) {
    expect(HttpHeaderHelper::isValidDateString($value))->toBe($expected);
})->with([
    ['', false],
    ['apple', false],
    ['Tue, 19 Jan 2038 03:14:07 GMT', true],
    ['Mon, 19 Jan 2038 03:14:07 GMT', false],
]);

it('replace headers', function (array $headers, array $replaceHeaders, array $expected) {
    expect(HttpHeaderHelper::replaceHeaders($headers, $replaceHeaders))->toBe($expected);
})->with([
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
]);
