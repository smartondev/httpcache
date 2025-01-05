<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('expires', function (\DateTime|int|string $expires, array $expectedHeaders) {
    $builder = new CacheHeaderBuilder();
    expect($builder->expires($expires)->toHeaders())->toBe($expectedHeaders)
        ->and($builder->toHeaders())->toBe($expectedHeaders);
})->with([
    'datetime object' => [
        new DateTime('2021-01-01 00:00:00'),
        ['expires' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
    'timestamp' => [
        1609459200,
        ['expires' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
    'string' => [
        'Fri, 01 Jan 2021 00:00:00 GMT',
        ['expires' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
]);

it('with expires', function (\DateTime|int|string $expires, array $expectedHeaders) {
    $builder = new CacheHeaderBuilder();
    expect($builder->withExpires($expires)->toHeaders())->toBe($expectedHeaders)
        ->and($builder->toHeaders())->toBeEmpty();
})->with([
    'datetime object' => [
        new DateTime('2021-01-01 00:00:00'),
        ['expires' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
    'timestamp' => [
        1609459200,
        ['expires' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
    'string' => [
        'Fri, 01 Jan 2021 00:00:00 GMT',
        ['expires' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
]);

it('reset expires', function () {
    $builder = (new CacheHeaderBuilder())
        ->expires(new DateTime('2021-01-01 00:00:00'));
    expect($builder->toHeaders())->toBe(['expires' => 'Fri, 01 Jan 2021 00:00:00 GMT']);
    $builder->resetExpires();
    expect($builder->toHeaders())->toBe([]);
});

it('without expires', function () {
    $builder = (new CacheHeaderBuilder())
        ->expires(new DateTime('2021-01-01 00:00:00'));
    expect($builder->toHeaders())->toBe(['expires' => 'Fri, 01 Jan 2021 00:00:00 GMT'])
        ->and($builder->withoutExpires()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['expires' => 'Fri, 01 Jan 2021 00:00:00 GMT']);
});

it('expires with invalid date', function (string $input, string $expectedExceptionClass, string $expectedExceptionMessage) {
    expect(fn() => (new CacheHeaderBuilder())->expires($input))->toThrow(
        exception: $expectedExceptionClass,
        exceptionMessage: $expectedExceptionMessage
    );
})->with([
    'malformed string' => ['malformed string', \SmartonDev\HttpCache\Exceptions\DateMalformedStringException::class, 'Malformed date string'],
    'empty' => ['', \InvalidArgumentException::class, 'Date string is empty'],
    'blank' => ['  ', \InvalidArgumentException::class, 'Date string is empty']
]);

it('with expires with invalid date', function (string $input, string $expectedExceptionClass, string $expectedExceptionMessage) {
    expect(fn() => (new CacheHeaderBuilder())->withExpires($input))->toThrow(
        exception: $expectedExceptionClass,
        exceptionMessage: $expectedExceptionMessage
    );
})->with([
    'malformed string' => ['malformed string', \SmartonDev\HttpCache\Exceptions\DateMalformedStringException::class, 'Malformed date string'],
    'empty' => ['', \InvalidArgumentException::class, 'Date string is empty'],
    'blank' => ['  ', \InvalidArgumentException::class, 'Date string is empty']
]);