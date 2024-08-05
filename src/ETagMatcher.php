<?php

namespace SmartonDev\HttpCache;

class ETagMatcher
{
    private const IF_MATCH_HEADER = 'if-match';
    private const IF_NONE_MATCH_HEADER = 'if-none-match';

    private array $headers = [];

    public function headers(array $headers): static
    {
        $this->headers = $headers;
        return $this;
    }

    public function withHeaders(array $headers): static
    {
        return (clone $this)->headers($headers);
    }

    public function ifMatchHeader(string|array $ifMatch): static
    {
        return (clone $this)
            ->headers([self::IF_MATCH_HEADER, $ifMatch]);
    }

    public function resetHeaders(): static
    {
        $this->headers = [];
        return $this;
    }

    public function withoutHeaders(): static
    {
        return (clone $this)->resetHeaders();
    }

    public function ifNoneMatchHeaderValue(string|array $ifNoneMatch): static
    {
        return (clone $this)
            ->headers([self::IF_NONE_MATCH_HEADER, $ifNoneMatch]);
    }

    public function withIfNoneMatchHeaderValue(string|array $ifNoneMatch): static
    {
        return (clone $this)->ifNoneMatchHeaderValue($ifNoneMatch);
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