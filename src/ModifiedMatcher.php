<?php

namespace SmartonDev\HttpCache;

class ModifiedMatcher
{
    private const IF_MODIFIED_SINCE_HEADER = 'if-modified-since';
    private const IF_UNMODIFIED_SINCE_HEADER = 'if-unmodified-since';

    private array $headers = [];

    public function withHeaders(array $headers): static
    {
        $clone = clone $this;
        $clone->headers = $headers;
        return $clone;
    }

    public function withIfModifiedSinceHeader(string|array $ifModifiedSince): static
    {
        $clone = clone $this;
        $clone->headers = [self::IF_MODIFIED_SINCE_HEADER, $ifModifiedSince];
        return $clone;
    }

    public function withIfUnmodifiedSinceHeader(string|array $ifUnmodifiedSince): static
    {
        $clone = clone $this;
        $clone->headers = [self::IF_UNMODIFIED_SINCE_HEADER, $ifUnmodifiedSince];
        return $clone;
    }

    public function getIfModifiedSinceHeader(): ?string
    {
        return getHeaderFirstValue($this->headers, self::IF_MODIFIED_SINCE_HEADER);
    }

    public function hasIfModifiedSinceHeader(): bool
    {
        return $this->getIfModifiedSinceHeader() !== null;
    }

    public function isValidIfModifiedSinceHeader(): bool
    {
        $value = $this->getIfModifiedSinceHeader();
        if (null === $value) {
            return false;
        }
        return isValidHttpHeaderDate($value);
    }

    public function isInvalidIfModifiedSinceHeader(): bool
    {
        return !$this->isValidIfModifiedSinceHeader();
    }

    public function getIfModifiedSinceHeaderAsTimestamp(): ?int
    {
        if (!$this->hasIfModifiedSinceHeader()) {
            return null;
        }
        if (!$this->isValidIfModifiedSinceHeader()) {
            throw new \RuntimeException('Invalid If-Modified-Since header');
        }
        return toTimestamp($this->getIfModifiedSinceHeader());
    }

    public function getIfUnmodifiedSinceHeader(): ?string
    {
        return getHeaderFirstValue($this->headers, self::IF_UNMODIFIED_SINCE_HEADER);
    }

    public function hasIfUnmodifiedSinceHeader(): bool
    {
        return $this->getIfUnmodifiedSinceHeader() !== null;
    }

    public function getIfUnmodifiedSinceHeaderAsTimestamp(): ?int
    {
        if (!$this->hasIfUnmodifiedSinceHeader()) {
            return null;
        }
        if (!$this->isValidIfUnmodifiedSinceHeader()) {
            throw new \RuntimeException('Invalid If-Unmodified-Since header');
        }
        return toTimestamp($this->getIfUnmodifiedSinceHeader());
    }

    public function isValidIfUnmodifiedSinceHeader(): bool
    {
        $value = $this->getIfUnmodifiedSinceHeader();
        if (null === $value) {
            return false;
        }
        return isValidHttpHeaderDate($value);
    }

    public function isInvalidIfUnmodifiedSinceHeader(): bool
    {
        return !$this->isValidIfUnmodifiedSinceHeader();
    }

    public function matches(int|string|\DateTime $baseData): ModifiedMatcherResult
    {
        $baseTimestamp = toTimestamp($baseData);
        $ifModifiedSince = $this->getIfModifiedSinceHeaderAsTimestamp();
        $ifUnmodifiedSince = $this->getIfUnmodifiedSinceHeaderAsTimestamp();

        return new ModifiedMatcherResult($baseTimestamp, $ifModifiedSince, $ifUnmodifiedSince);
    }
}