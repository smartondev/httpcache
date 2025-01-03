<?php

declare(strict_types=1);
namespace SmartonDev\HttpCache\Matchers;

use SmartonDev\HttpCache\Exceptions\DateMalformedStringException;
use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;
use SmartonDev\HttpCache\Helpers\TimeHelper;

class ModifiedMatcher extends MatcherHeaderAbstract
{
    private const IF_MODIFIED_SINCE_HEADER = 'if-modified-since';
    private const IF_UNMODIFIED_SINCE_HEADER = 'if-unmodified-since';

    /**
     * Set If-Modified-Since header.
     *
     * @param string|array<string> $ifModifiedSinceHeaderValue if-modified-since header value or values
     */
    public function ifModifiedSinceHeader(string|array $ifModifiedSinceHeaderValue): static
    {
        return $this->headers(
            HttpHeaderHelper::replaceHeaders($this->getHeaders(), [self::IF_MODIFIED_SINCE_HEADER => $ifModifiedSinceHeaderValue])
        );
    }

    /**
     * New instance with If-Modified-Since header.
     *
     * @param string|array<string> $ifModifiedSinceHeaderValue if-modified-since header value or values
     */
    public function withIfModifiedSinceHeader(string|array $ifModifiedSinceHeaderValue): static
    {
        return (clone $this)->ifModifiedSinceHeader($ifModifiedSinceHeaderValue);
    }

    /**
     * Set If-Unmodified-Since header.
     *
     * @param string|array<string> $ifUnmodifiedSinceHeaderValue if-unmodified-since header value or values
     */
    public function ifUnmodifiedSinceHeader(string|array $ifUnmodifiedSinceHeaderValue): static
    {
        return $this->headers(
            HttpHeaderHelper::replaceHeaders($this->getHeaders(), [self::IF_UNMODIFIED_SINCE_HEADER => $ifUnmodifiedSinceHeaderValue])
        );
    }

    /**
     * New instance with If-Unmodified-Since header.
     *
     * @param string|array<string> $ifUnmodifiedSinceHeaderValue if-unmodified-since header value or values
     */
    public function withIfUnmodifiedSinceHeader(string|array $ifUnmodifiedSinceHeaderValue): static
    {
        return (clone $this)->ifUnmodifiedSinceHeader($ifUnmodifiedSinceHeaderValue);
    }

    public function getIfModifiedSinceHeader(): ?string
    {
        return HttpHeaderHelper::getFirstHeaderValue($this->getHeaders(), self::IF_MODIFIED_SINCE_HEADER);
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

    /**
     * @throws DateMalformedStringException
     */
    public function getIfModifiedSinceHeaderAsTimestamp(): ?int
    {
        if (!$this->hasIfModifiedSinceHeader()) {
            return null;
        }
        if (!$this->isValidIfModifiedSinceHeader()) {
            throw new DateMalformedStringException('Invalid If-Modified-Since header value');
        }
        $time = $this->getIfModifiedSinceHeader();
        if (null === $time) {
            throw new \LogicException('If-Modified-Since header is empty');
        }
        return TimeHelper::toTimestamp($time);
    }

    public function getIfUnmodifiedSinceHeader(): ?string
    {
        return HttpHeaderHelper::getFirstHeaderValue($this->getHeaders(), self::IF_UNMODIFIED_SINCE_HEADER);
    }

    public function hasIfUnmodifiedSinceHeader(): bool
    {
        return $this->getIfUnmodifiedSinceHeader() !== null;
    }

    /**
     * @throws DateMalformedStringException
     */
    public function getIfUnmodifiedSinceHeaderAsTimestamp(): ?int
    {
        if (!$this->hasIfUnmodifiedSinceHeader()) {
            return null;
        }
        if (!$this->isValidIfUnmodifiedSinceHeader()) {
            throw new DateMalformedStringException('Invalid If-Unmodified-Since header value');
        }
        $time = $this->getIfUnmodifiedSinceHeader();
        if (null === $time) {
            throw new \LogicException('If-Unmodified-Since header is empty');
        }
        return TimeHelper::toTimestamp($time);
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

    /**
     * Modified match result.
     * @throws DateMalformedStringException
     */
    public function matches(int|string|\DateTime $baseData): ModifiedMatcherResult
    {
        $baseTimestamp = TimeHelper::toTimestamp($baseData);
        $ifModifiedSince = $this->getIfModifiedSinceHeaderAsTimestamp();
        $ifUnmodifiedSince = $this->getIfUnmodifiedSinceHeaderAsTimestamp();

        return new ModifiedMatcherResult($baseTimestamp, $ifModifiedSince, $ifUnmodifiedSince);
    }
}