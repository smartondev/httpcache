<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Exceptions\DateMalformedStringException;
use SmartonDev\HttpCache\Matchers\ModifiedMatcher;

it('has if modified since header', function (array $headers, bool $expected) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);
    expect($matcher->hasIfModifiedSinceHeader())->toBe($expected);
})->with([
    'has' => [
        ['If-Modified-Since' => 'Sat, 29 Oct 1994 19:43:31 GMT'],
        true,
    ],
    'has empty' => [
        ['If-Modified-Since' => ''],
        true,
    ],
    'empty' => [
        [],
        false,
    ],
    'another header' => [
        ['x-foo' => 'bar'],
        false,
    ],
]);

it('has if unmodified since header', function (array $headers, bool $expected) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);
    expect($matcher->hasIfUnmodifiedSinceHeader())->toBe($expected);
})->with([
    'has' => [
        ['If-Unmodified-Since' => 'Sat, 29 Oct 1994 19:43:31 GMT'],
        true,
    ],
    'has empty' => [
        ['If-Unmodified-Since' => ''],
        true,
    ],
    'empty' => [
        [],
        false,
    ],
    'another header' => [
        ['x-foo' => 'bar'],
        false,
    ],
]);

it('is valid/invalid if modified since header', function (array $headers, bool $expected) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);
    expect($matcher->isValidIfModifiedSinceHeader())->toBe($expected)
        ->and($matcher->isInvalidIfModifiedSinceHeader())->toBe(!$expected);
})->with([
    'valid' => [
        ['If-Modified-Since' => 'Sat, 29 Oct 1994 19:43:31 GMT'],
        true,
    ],
    'invalid date string' => [
        ['If-Modified-Since' => 'apple'],
        false,
    ],
    'invalid empty string' => [
        ['If-Modified-Since' => ''],
        false,
    ],
    'empty' => [
        [],
        false,
    ],
    'another header' => [
        ['x-foo' => 'bar'],
        false,
    ],
]);

it('is valid/invalid if unmodified since header', function (array $headers, bool $expected) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);
    expect($matcher->isValidIfUnmodifiedSinceHeader())->toBe($expected)
        ->and($matcher->isInvalidIfUnmodifiedSinceHeader())->toBe(!$expected);
})->with([
    'valid' => [
        ['If-Unmodified-Since' => 'Sat, 29 Oct 1994 19:43:31 GMT'],
        true,
    ],
    'invalid date string' => [
        ['If-Unmodified-Since' => 'apple'],
        false,
    ],
    'invalid empty string' => [
        ['If-Unmodified-Since' => ''],
        false,
    ],
    'empty' => [
        [],
        false,
    ],
    'another header' => [
        ['x-foo' => 'bar'],
        false,
    ],
]);

it('get if modified since header', function (array $headers, ?string $expected) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);
    expect($matcher->getIfModifiedSinceHeader())->toBe($expected);
})->with([
    'has' => [
        ['If-Modified-Since' => 'Sat, 29 Oct 1994 19:43:31 GMT'],
        'Sat, 29 Oct 1994 19:43:31 GMT',
    ],
    'has empty' => [
        ['If-Modified-Since' => ''],
        '',
    ],
    'empty' => [
        [],
        null,
    ],
    'another header' => [
        ['x-foo' => 'bar'],
        null,
    ],
]);

it('get if unmodified since header', function (array $headers, ?string $expected) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);
    expect($matcher->getIfUnmodifiedSinceHeader())->toBe($expected);
})->with([
    'has' => [
        ['If-Unmodified-Since' => 'Sat, 29 Oct 1994 19:43:31 GMT'],
        'Sat, 29 Oct 1994 19:43:31 GMT',
    ],
    'has empty' => [
        ['If-Unmodified-Since' => ''],
        '',
    ],
    'empty' => [
        [],
        null,
    ],
    'another header' => [
        ['x-foo' => 'bar'],
        null,
    ],
]);

it('get if modified since header as timestamp', function (array $headers, ?int $expected) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);
    expect($matcher->getIfModifiedSinceHeaderAsTimestamp())->toBe($expected);
})->with([
    'has' => [
        ['If-Modified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT'],
        784903526,
    ],
    'empty' => [
        [],
        null,
    ],
    'another header' => [
        ['x-foo' => 'bar'],
        null,
    ],
]);

it('get if modified since header as timestamp invalid date string', function (array $headers) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);

    expect(fn() => $matcher->getIfModifiedSinceHeaderAsTimestamp())
        ->toThrow(
            exception: DateMalformedStringException::class,
            exceptionMessage: 'Invalid If-Modified-Since header value',
        );
})->with([
    'any string' => [
        ['If-Modified-Since' => 'apple'],
    ],
    'empty string' => [
        ['If-Modified-Since' => ''],
    ],
]);

it('get if unmodified since header as timestamp', function (array $headers, ?int $expected) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);
    expect($matcher->getIfUnmodifiedSinceHeaderAsTimestamp())->toBe($expected);
})->with([
    'has' => [
        ['If-Unmodified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT'],
        784903526,
    ],
    'empty' => [
        [],
        null,
    ],
    'another header' => [
        ['x-foo' => 'bar'],
        null,
    ],
]);

it('get if unmodified since header as timestamp invalid date string', function (array $headers) {
    $matcher = (new ModifiedMatcher())
        ->headers($headers);

    expect(fn() => $matcher->getIfUnmodifiedSinceHeaderAsTimestamp())
        ->toThrow(
            exception: DateMalformedStringException::class,
            exceptionMessage: 'Invalid If-Unmodified-Since header value',
        );
})->with([
    'any string' => [
        ['If-Unmodified-Since' => 'apple'],
    ],
    'empty string' => [
        ['If-Unmodified-Since' => ''],
    ],
]);

it('matches if modified since', function (string $value, string $before, string $after) {
    $matcher = (new ModifiedMatcher())
        ->headers([
            'If-Modified-Since' => $value,
        ]);
    $dtBefore = new \DateTime($before);
    $dtAfter = new \DateTime($after);
    $dtEq = new \DateTime($value);
    expect($matcher->matches($dtEq)->matchesModifiedAt())->toBeTrue()
        ->and($matcher->matches($dtBefore)->matchesModifiedAt())->toBeFalse()
        ->and($matcher->matches($dtAfter)->isModifiedSince())->toBeTrue();
})->with([
    'one' => [
        'Tue, 15 Nov 1994 12:45:26 GMT',
        'Mon, 14 Nov 1994 12:45:26 GMT',
        'Wed, 16 Nov 1994 12:45:26 GMT',
    ],
]);

it('matches if unmodified since', function (string $value, string $before, string $after) {
    $matcher = (new ModifiedMatcher())
        ->headers([
            'If-Unmodified-Since' => $value,
        ]);
    $dtBefore = new \DateTime($before);
    $dtAfter = new \DateTime($after);
    $dtEq = new \DateTime($value);
    expect($matcher->matches($dtBefore)->isUnmodifiedSince())->toBeTrue()
        ->and($matcher->matches($dtAfter)->isUnmodifiedSince())->toBeFalse()
        ->and($matcher->matches($dtEq)->isModifiedSince())->toBeFalse();
})->with([
    'one' => [
        'Tue, 15 Nov 1994 12:45:26 GMT',
        'Mon, 14 Nov 1994 12:45:26 GMT',
        'Wed, 16 Nov 1994 12:45:26 GMT',
    ],
]);

it('if modified since header mutable/immutable', function () {
    $matcher = (new ModifiedMatcher())
        ->headers([
            'If-Modified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT',
        ]);
    $matcher2 = $matcher->withIfModifiedSinceHeader('Wed, 16 Nov 1994 12:45:26 GMT');
    expect($matcher->getIfModifiedSinceHeader())->toBe('Tue, 15 Nov 1994 12:45:26 GMT')
        ->and($matcher2->getIfModifiedSinceHeader())->toBe('Wed, 16 Nov 1994 12:45:26 GMT');
});

it('if unmodified since header mutable/immutable', function () {
    $matcher = (new ModifiedMatcher())
        ->headers([
            'If-Unmodified-Since' => 'Tue, 15 Nov 1994 12:45:26 GMT',
        ]);
    $matcher2 = $matcher->withIfUnmodifiedSinceHeader('Wed, 16 Nov 1994 12:45:26 GMT');
    expect($matcher->getIfUnmodifiedSinceHeader())->toBe('Tue, 15 Nov 1994 12:45:26 GMT')
        ->and($matcher2->getIfUnmodifiedSinceHeader())->toBe('Wed, 16 Nov 1994 12:45:26 GMT');
});
