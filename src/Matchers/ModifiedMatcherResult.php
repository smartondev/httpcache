<?php

declare(strict_types=1);
namespace SmartonDev\HttpCache\Matchers;

readonly class ModifiedMatcherResult
{
    public function __construct(
        private int  $baseDate,
        private ?int $ifModifiedSince,
        private ?int $ifUnmodifiedSince)
    {
    }

    public function isModifiedSince(): bool
    {
        return ($this->ifModifiedSince !== null && $this->baseDate > $this->ifModifiedSince)
            || ($this->ifUnmodifiedSince !== null && $this->baseDate > $this->ifUnmodifiedSince);
    }

    public function matchesModifiedAt(): bool
    {
        return $this->ifModifiedSince !== null && $this->baseDate === $this->ifModifiedSince;
    }

    public function isUnmodifiedSince(): bool
    {
        return $this->ifUnmodifiedSince !== null && $this->baseDate <= $this->ifUnmodifiedSince;
    }
}