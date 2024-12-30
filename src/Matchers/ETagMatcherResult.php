<?php

declare(strict_types=1);
namespace SmartonDev\HttpCache\Matchers;

readonly class ETagMatcherResult
{
    public function __construct(
        private bool $matchesIfMatchHeader,
        private bool $matchesIfNoneMatchHeader,
    )
    {
    }

    public function matchesIfMatchHeader(): bool
    {
        return $this->matchesIfMatchHeader;
    }

    public function notMatchesIfMatchHeader(): bool
    {
        return !$this->matchesIfMatchHeader;
    }

    public function matchesIfNoneMatchHeader(): bool
    {
        return $this->matchesIfNoneMatchHeader;
    }

    public function notMatchesIfNoneMatchHeader(): bool
    {
        return !$this->matchesIfNoneMatchHeader;
    }
}