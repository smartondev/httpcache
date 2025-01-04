<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\ETagHeaderBuilder;

it('etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456');
    expect($builder->toHeaders())->toBe(['etag' => '"123456"']);
});

it('etag null/string', function (?string $etag) {
    $builder = (new ETagHeaderBuilder())
        ->etag($etag);
    expect($builder->toHeaders())->toBeEmpty();
})->with([
    'null' => null,
    'empty' => '',
    'blank' => '     ',
]);

it('with etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456');
    $builder2 = $builder->withETag('654321');
    expect($builder->toHeaders())->toBe(['etag' => '"123456"'])
        ->and($builder2->toHeaders())->toBe(['etag' => '"654321"']);
});

it('computed etag', function (mixed $data, callable $compute, array $expectedHeaders) {
    $builder = (new ETagHeaderBuilder())
        ->computedETag($data, $compute);
    expect($builder->toHeaders())->toBe($expectedHeaders);
})->with([
    'md5' => [
        'content123',
        'md5',
        ['etag' => '"' . md5('content123') . '"'],
    ],
    'sha1' => [
        'content789',
        'sha1',
        ['etag' => '"' . sha1('content789') . '"'],
    ],
    'int multiple' => [
        2,
        fn($d) => strval($d * 11),
        ['etag' => '"22"'],
    ],
]);

it('with computed etag', function (mixed $data, callable $compute, array $expectedHeaders) {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456');
    $builder2 = $builder->withComputedETag($data, $compute);
    expect($builder->toHeaders())->toBe(['etag' => '"123456"'])
        ->and($builder2->toHeaders())->toBe($expectedHeaders);
})->with([
    'md5' => [
        'content123',
        'md5',
        ['etag' => '"' . md5('content123') . '"'],
    ],
    'sha1' => [
        'content789',
        'sha1',
        ['etag' => '"' . sha1('content789') . '"'],
    ],
    'int multiple' => [
        2,
        fn($d) => strval($d * 11),
        ['etag' => '"22"'],
    ],
]);

it('computed etag with invalid return value', function (mixed $return) {
    $builder = (new ETagHeaderBuilder());
    expect(fn() => $builder->computedETag('content123', fn() => $return))->toThrow(
        exception: InvalidArgumentException::class,
        message: 'ETag must be a string or null',
    );
})->with([
    'array' => [[]],
    'object' => [new stdClass()],
    'int' => [123],
    'float' => [123.456],
    'bool' => [true],
]);

it('week etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456')
        ->weekETag();
    expect($builder->toHeaders())->toBe(['etag' => 'W/"123456"']);
});

it('is empty', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456');
    expect($builder->isEmpty())->toBeFalse()
        ->and($builder->withEtag(null)->isEmpty())->toBeTrue();
});

it('is not empty', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag(null);
    expect($builder->isNotEmpty())->toBeFalse()
        ->and($builder->withEtag('123456')->isNotEmpty())->toBeTrue();
});

it('reset etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456');
    expect($builder->toHeaders())->not()->toBeEmpty();
    $builder->resetETag();
    expect($builder->toHeaders())->toBeEmpty();
});

it('without etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456');
    expect($builder->withoutETag()->toHeaders())->toBeEmpty()
        ->and($builder->toHeaders())->not()->toBeEmpty();
});

it('weak etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456')
        ->weekETag();
    expect($builder->toHeaders())->toBe(['etag' => 'W/"123456"']);
});

it('with weak etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456');
    expect($builder->withWeekETag()->toHeaders())->toBe(['etag' => 'W/"123456"'])
        ->and($builder->toHeaders())->toBe(['etag' => '"123456"']);
});

it('without week etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456')
        ->weekETag();
    expect($builder->withoutWeekETag()->toHeaders())->toBe(['etag' => '"123456"']);
});

it('reset week etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456')
        ->weekETag();
    expect($builder->toHeaders())->toBe(['etag' => 'W/"123456"']);
    $builder->resetWeekETag();
    expect($builder->toHeaders())->toBe(['etag' => '"123456"']);
});

it('initial empty', function () {
    $builder = new ETagHeaderBuilder();
    expect($builder->toHeaders())->toBeEmpty();
});

it('to string', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456');
    expect((string)$builder)->toBe('"123456"')
        ->and((string)$builder->withWeekETag())->toBe('W/"123456"');
});

it('get etag', function () {
    $builder = (new ETagHeaderBuilder())
        ->etag('123456');
    expect($builder->getETag())->toBe('"123456"')
        ->and($builder->withWeekETag()->getETag())->toBe('W/"123456"');
});

it('get etag if not set', function () {
    $builder = new ETagHeaderBuilder();
    expect($builder->getETag())->toBeNull();
});
