<?php

declare(strict_types=1);
namespace SmartonDev\HttpCache\Matchers;

use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;

class ETagMatcher extends MatcherHeaderAbstract
{
    private const IF_MATCH_HEADER = 'if-match';
    private const IF_NONE_MATCH_HEADER = 'if-none-match';

    /**
     * Set If-Match header.
     *
     * @param string|array<string> $ifMatch if-match header value or values
     */
    public function ifMatchHeader(string|array $ifMatch): static
    {
        $this->headers = HttpHeaderHelper::replaceHeaders($this->headers, [self::IF_MATCH_HEADER => $ifMatch]);
        return $this;
    }

    /**
     * New instance with If-Match header.
     *
     * @param string|array<string> $ifMatch if-match header value or values
     */
    public function withIfMatchHeader(string|array $ifMatch): static
    {
        return (clone $this)->ifMatchHeader($ifMatch);
    }

    /**
     * Set If-None-Match header.
     *
     * @param string|array<string> $ifNoneMatch if-none-match header value or values
     */
    public function ifNoneMatchHeaderValue(string|array $ifNoneMatch): static
    {
        $this->headers = HttpHeaderHelper::replaceHeaders($this->headers, [self::IF_NONE_MATCH_HEADER => $ifNoneMatch]);
        return $this;
    }

    /**
     * New instance with If-None-Match header.
     *
     * @param string|array<string> $ifNoneMatch if-none-match header value or values
     */
    public function withIfNoneMatchHeaderValue(string|array $ifNoneMatch): static
    {
        return (clone $this)->ifNoneMatchHeaderValue($ifNoneMatch);
    }

    public function getIfNoneMatchHeader(): ?string
    {
        return HttpHeaderHelper::getFirstHeaderValue($this->headers, self::IF_NONE_MATCH_HEADER);
    }

    public function hasIfNoneMatchHeader(): bool
    {
        return $this->getIfNoneMatchHeader() !== null;
    }

    public function getIfMatchHeader(): ?string
    {
        return HttpHeaderHelper::getFirstHeaderValue($this->headers, self::IF_MATCH_HEADER);
    }

    public function hasIfMatchHeader(): bool
    {
        return $this->getIfMatchHeader() !== null;
    }

    /**
     * Etag header match result.
     * @param null|string|array<string, string|array<string>> $etag
     */
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