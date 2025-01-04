<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Builders\CacheHeaderBuilder;
use SmartonDev\HttpCache\Builders\ETagHeaderBuilder;

it('etag', function (string $etag, array $expectedHeaders) {
    $builder = new CacheHeaderBuilder();
    expect($builder->etag($etag)->toHeaders())->toBe($expectedHeaders)
        ->and($builder->toHeaders())->toBe($expectedHeaders);
})->with([
    'strong' => ['"123"', ['etag' => '"123"']],
    'weak' => ['W/"123"', ['etag' => 'W/"123"']],
]);

it('with etag', function (string $etag, array $expectedHeaders) {
    $builder = new CacheHeaderBuilder();
    expect($builder->withEtag($etag)->toHeaders())->toBe($expectedHeaders)
        ->and($builder->toHeaders())->toBeEmpty();
})->with([
    'strong' => ['"123"', ['etag' => '"123"']],
    'weak' => ['W/"123"', ['etag' => 'W/"123"']],
]);

it('reset etag', function () {
    $builder = (new CacheHeaderBuilder())
        ->etag('"123"');
    expect($builder->toHeaders())->toBe(['etag' => '"123"']);
    $builder->resetEtag();
    expect($builder->toHeaders())->toBe([]);
});

it('without etag', function () {
    $builder = (new CacheHeaderBuilder())
        ->etag('"123"');
    expect($builder->toHeaders())->toBe(['etag' => '"123"'])
        ->and($builder->withoutEtag()->toHeaders())->toBe([])
        ->and($builder->toHeaders())->toBe(['etag' => '"123"']);
});

it('invalid etag', function (string $etag) {
    expect(fn() => (new CacheHeaderBuilder())->etag($etag))
        ->toThrow(
            exception: InvalidArgumentException::class,
            exceptionMessage: 'ETag must be a quoted string with optional weak indicator',
        );
})->with([
    'numbers' => ['123'],
    'word' => ['apple'],
    'start with W/' => ['W/123'],
    'start with "W/' => ['"W/123'],
    'end with quote' => ['123"'],
    'start with quote' => ['"123'],
]);

it('etag with builder', function () {
    $etag = (new ETagHeaderBuilder())
        ->etag('123');
    $builder = new CacheHeaderBuilder();
    expect($builder->etag($etag)->toHeaders())->toBe(['etag' => '"123"'])
        ->and($builder->etag($etag->weekETag())->toHeaders())->toBe(['etag' => 'W/"123"']);
});

it('with etag with builder', function () {
    $etag = (new ETagHeaderBuilder())
        ->etag('123');
    $builder = new CacheHeaderBuilder();
    expect($builder->withEtag($etag)->toHeaders())->toBe(['etag' => '"123"'])
        ->and($builder->withEtag($etag->weekETag())->toHeaders())->toBe(['etag' => 'W/"123"']);
});
