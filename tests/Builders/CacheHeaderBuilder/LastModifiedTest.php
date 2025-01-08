<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;

it('last modified', function (\DateTime|int|string $input, array $expectedHeaders) {
    $builder = new CacheHeaderBuilder();
    expect($builder->lastModified($input)->toHeaders())->toBe($expectedHeaders)
        ->and($builder->toHeaders())->toBe($expectedHeaders);
})->with([
    'datetime object' => [
        new DateTime('2021-01-01 00:00:00'),
        ['last-modified' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
    'timestamp' => [
        1609459200,
        ['last-modified' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
    'string' => [
        'Fri, 01 Jan 2021 00:00:00 GMT',
        ['last-modified' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
]);

it('with last modified', function (\DateTime|int|string $input, array $expectedHeaders) {
    $builder = new CacheHeaderBuilder();
    expect($builder->withLastModified($input)->toHeaders())->toBe($expectedHeaders)
        ->and($builder->toHeaders())->toBeEmpty();
})->with([
    'datetime object' => [
        new DateTime('2021-01-01 00:00:00'),
        ['last-modified' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
    'timestamp' => [
        1609459200,
        ['last-modified' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
    'string' => [
        'Fri, 01 Jan 2021 00:00:00 GMT',
        ['last-modified' => 'Fri, 01 Jan 2021 00:00:00 GMT'],
    ],
]);

it('reset last modified', function () {
    $builder = (new CacheHeaderBuilder())
        ->lastModified(new DateTime('2021-01-01 00:00:00'));
    expect($builder->toHeaders())->toBe(['last-modified' => 'Fri, 01 Jan 2021 00:00:00 GMT']);
    $builder->resetLastModified();
    expect($builder->toHeaders())->toBe([]);
});

it('without last modified', function () {
    $builder = (new CacheHeaderBuilder())
        ->lastModified(new DateTime('2021-01-01 00:00:00'));
    expect($builder->toHeaders())->toBe(['last-modified' => 'Fri, 01 Jan 2021 00:00:00 GMT'])
        ->and($builder->withoutLastModified()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['last-modified' => 'Fri, 01 Jan 2021 00:00:00 GMT']);
});

it('last modified with invalid date', function (string $input, string $expectedExceptionClass, string $expectedExceptionMessage) {
    expect(fn() => (new CacheHeaderBuilder())->lastModified($input))->toThrow(
        exception: $expectedExceptionClass,
        exceptionMessage: $expectedExceptionMessage
    );
})->with([
    'malformed string' => ['malformed string', \SmartonDev\HttpCache\Exceptions\DateMalformedStringException::class, 'Malformed date string'],
    'empty' => ['', \InvalidArgumentException::class, 'Date string is empty'],
    'blank' => ['  ', \InvalidArgumentException::class, 'Date string is empty']
]);

it('with last modified with invalid date', function (string $input, string $expectedExceptionClass, string $expectedExceptionMessage) {
    expect(fn() => (new CacheHeaderBuilder())->withLastModified($input))->toThrow(
        exception: $expectedExceptionClass,
        exceptionMessage: $expectedExceptionMessage
    );
})->with([
    'malformed string' => ['malformed string', \SmartonDev\HttpCache\Exceptions\DateMalformedStringException::class, 'Malformed date string'],
    'empty' => ['', \InvalidArgumentException::class, 'Date string is empty'],
    'blank' => ['  ', \InvalidArgumentException::class, 'Date string is empty']
]);

it('has last modified', function () {
    $builder = (new CacheHeaderBuilder())
        ->lastModified(new DateTime('2021-01-01 00:00:00'));
    expect($builder->hasLastModified())->toBeTrue()
        ->and($builder->resetLastModified()->hasLastModified())->toBeFalse();
});