<?php

namespace SmartonDev\HttpCache;

readonly class ModifiedMatcherResult
{
    public function __construct(
        private int  $baseDate,
        private ?int $ifModifiedSince,
        private ?int $ifUnmodifiedSince)
    {
    }

    public function isBeforeModifiedSince(): bool
    {
        return $this->ifModifiedSince !== null && $this->baseDate < $this->ifModifiedSince;
    }

    public function isAfterModifiedSince(): bool
    {
        return $this->ifModifiedSince !== null && $this->baseDate > $this->ifModifiedSince;
    }

    public function isEqualsModifiedSince(): bool
    {
        return $this->ifModifiedSince !== null && $this->baseDate === $this->ifModifiedSince;
    }

    public function isBeforeUnmodifiedSince(): bool
    {
        return $this->ifUnmodifiedSince !== null && $this->baseDate < $this->ifUnmodifiedSince;
    }

    public function isAfterUnmodifiedSince(): bool
    {
        return $this->ifUnmodifiedSince !== null && $this->baseDate > $this->ifUnmodifiedSince;
    }

    public function isEqualsUnmodifiedSince(): bool
    {
        return $this->ifUnmodifiedSince !== null && $this->baseDate === $this->ifUnmodifiedSince;
    }
}