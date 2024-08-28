<?php

namespace SmartonDev\HttpCache\Matchers;

use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;
use SmartonDev\HttpCache\Helpers\TimeHelper;

class ModifiedMatcher extends MatcherHeaderAbstract
{
    private const IF_MODIFIED_SINCE_HEADER = 'if-modified-since';
    private const IF_UNMODIFIED_SINCE_HEADER = 'if-unmodified-since';

    public function ifModifiedSinceHeader(string|array $ifModifiedSinceHeaderValue): static
    {
        $this->headers = HttpHeaderHelper::replaceHeaders($this->headers, [self::IF_MODIFIED_SINCE_HEADER => $ifModifiedSinceHeaderValue]);
        return $this;
    }

    public function withIfModifiedSinceHeader(string|array $ifModifiedSinceHeaderValue): static
    {
        return (clone $this)->ifModifiedSinceHeader($ifModifiedSinceHeaderValue);
    }

    public function ifUnmodifiedSinceHeader(string|array $ifUnmodifiedSinceHeaderValue): static
    {
        $this->headers = HttpHeaderHelper::replaceHeaders($this->headers, [self::IF_UNMODIFIED_SINCE_HEADER => $ifUnmodifiedSinceHeaderValue]);
        return $this;
    }

    public function withIfUnmodifiedSinceHeader(string|array $ifUnmodifiedSinceHeaderValue): static
    {
        return (clone $this)->ifUnmodifiedSinceHeader($ifUnmodifiedSinceHeaderValue);
    }

    public function getIfModifiedSinceHeader(): ?string
    {
        return HttpHeaderHelper::getFirstHeaderValue($this->headers, self::IF_MODIFIED_SINCE_HEADER);
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
        return HttpHeaderHelper::isValidDateString($value);
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
            throw new \RuntimeException('Invalid If-Modified-Since header value');
        }
        return TimeHelper::toTimestamp($this->getIfModifiedSinceHeader());
    }

    public function getIfUnmodifiedSinceHeader(): ?string
    {
        return HttpHeaderHelper::getFirstHeaderValue($this->headers, self::IF_UNMODIFIED_SINCE_HEADER);
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
            throw new \RuntimeException('Invalid If-Unmodified-Since header value');
        }
        return TimeHelper::toTimestamp($this->getIfUnmodifiedSinceHeader());
    }

    public function isValidIfUnmodifiedSinceHeader(): bool
    {
        $value = $this->getIfUnmodifiedSinceHeader();
        if (null === $value) {
            return false;
        }
        return HttpHeaderHelper::isValidDateString($value);
    }

    public function isInvalidIfUnmodifiedSinceHeader(): bool
    {
        return !$this->isValidIfUnmodifiedSinceHeader();
    }

    public function matches(int|string|\DateTime $baseData): ModifiedMatcherResult
    {
        $baseTimestamp = TimeHelper::toTimestamp($baseData);
        $ifModifiedSince = $this->getIfModifiedSinceHeaderAsTimestamp();
        $ifUnmodifiedSince = $this->getIfUnmodifiedSinceHeaderAsTimestamp();

        return new ModifiedMatcherResult($baseTimestamp, $ifModifiedSince, $ifUnmodifiedSince);
    }
}