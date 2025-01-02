<?php

declare(strict_types=1);

use SmartonDev\HttpCache\Matchers\ETagMatcher;

it('if match header', function (array $headers, ?string $etag, bool $expected) {
    $matcher = (new ETagMatcher())
        ->headers($headers);
    expect($matcher->matches($etag)->matchesIfMatchHeader())->toBe($expected)
        ->and($matcher->matches($etag)->notMatchesIfMatchHeader())->toBe(!$expected);
})->with([
    'match' => [
        ['If-Match' => '"123"'],
        '"123"',
        true,
    ],
    'not match' => [
        ['If-Match' => '"123"'],
        '"456"',
        false,
    ],
    'not match empty' => [
        [],
        '"123"',
        false,
    ],
    'another header' => [
        ['x-foo' => '"123"'],
        '"123"',
        false,
    ],
    'null' => [
        ['If-Match' => '"123"'],
        null,
        false,
    ],
    'null empty headers' => [
        [],
        null,
        false,
    ],
]);

it('if none match header', function (array $headers, ?string $etag, bool $expected) {
    $matcher = (new ETagMatcher())
        ->headers($headers);
    expect($matcher->matches($etag)->matchesIfNoneMatchHeader())->toBe($expected)
        ->and($matcher->matches($etag)->notMatchesIfNoneMatchHeader())->toBe(!$expected);
})->with([
    'match' => [
        ['If-None-Match' => '"123"'],
        '"123"',
        true,
    ],
    'not match' => [
        ['If-None-Match' => '"123"'],
        '"456"',
        false,
    ],
    'not match empty' => [
        [],
        '"123"',
        false,
    ],
    'another header' => [
        ['x-foo' => '"123"'],
        '"123"',
        false,
    ],
    'null' => [
        ['If-None-Match' => '"123"'],
        null,
        false,
    ],
    'null empty headers' => [
        [],
        null,
        false,
    ],
]);
