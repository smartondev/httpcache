<?php

namespace SmartonDev\HttpCache;

class ETagMatcher
{
    private const IF_MATCH_HEADER = 'If-Match';
    private const IF_NONE_MATCH_HEADER = 'If-None-Match';

    private array $headers = [];

    public function withHeaders(array $headers): static
    {
        $clone = clone $this;
        $clone->headers = $headers;
        return $clone;
    }

    public function withIfMatchHeader(string|array $ifMatch): static
    {
        $clone = clone $this;
        $clone->headers = [self::IF_MATCH_HEADER, $ifMatch];
        return $clone;
    }

    public function withIfNoneMatchHeader(string|array $ifNoneMatch): static
    {
        $clone = clone $this;
        $clone->headers = [self::IF_NONE_MATCH_HEADER, $ifNoneMatch];
        return $clone;
    }

    public function getIfNoneMatchHeader(): ?string
    {
        return getHeaderFirstValue($this->headers, self::IF_NONE_MATCH_HEADER);
    }

    public function hasIfNoneMatchHeader(): bool
    {
        return $this->getIfNoneMatchHeader() !== null;
    }

    public function getIfMatchHeader(): ?string
    {
        return getHeaderFirstValue($this->headers, self::IF_MATCH_HEADER);
    }

    public function hasIfMatchHeader(): bool
    {
        return $this->getIfMatchHeader() !== null;
    }

    public function matches(null|string|array $etag): ETagMatcherResult
    {
        if (null === $etag) {
            return new ETagMatcherResult(false, false);
        }
        if (is_string($etag)) {
            $etag = [$etag];
        }
        return new ETagMatcherResult(
            matchesIfMatchHeader: in_array($this->getIfMatchHeader(), $etag),
            matchesIfNoneMatchHeader: in_array($this->getIfNoneMatchHeader(), $etag),
        );
    }
}