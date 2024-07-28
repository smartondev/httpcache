<?php

namespace SmartonDev\HttpCache;

class CacheHeaderBuilder
{
    private const AGE_HEADER = 'Age';
    private const CACHE_CONTROL_HEADER = 'Cache-Control';

    private const LAST_MODIFIED_HEADER = 'Last-Modified';

    private const EXPIRES_HEADER = 'Expires';
    private bool $noCache = false;
    private null|int $maxAge = null;

    private null|int $sharedMaxAge = null;

    private bool $mustRevalidate = false;

    private bool $proxyRevalidate = false;

    private bool $noStore = false;

    private bool $private = false;

    private bool $public = false;

    private bool $mustUnderstand = false;

    private bool $immutable = false;

    private bool $noTransform = false;

    private int|null $staleWhileRevalidate = null;

    private int|null $staleIfError = null;

    private ?int $age = null;

    private ?int $lastModified = null;

    private ?int $expires = null;

    private ?ETagHeaderBuilder $ETagHeaderBuilder = null;

    public function withNoCache(): static
    {
        $clone = clone $this;
        $clone->noCache = true;
        $clone->maxAge = null;
        $clone->sharedMaxAge = null;
        $clone->mustRevalidate = false;
        $clone->proxyRevalidate = false;
        $clone->noStore = false;
        $clone->private = false;
        $clone->public = false;
        $clone->mustUnderstand = false;
        $clone->noTransform = false;
        $clone->immutable = false;
        $clone->staleWhileRevalidate = false;
        $clone->staleIfError = false;
        $clone->age = null;
        $clone->ETagHeaderBuilder = null;
        $clone->expires = null;
        return $clone;
    }

    public function withExpires(int|string|\Datetime $expires): static
    {
        $clone = clone $this;
        $clone->expires = toTimestamp($expires);
        return $clone;
    }

    public function withoutExpires(): static
    {
        $clone = clone $this;
        $clone->expires = null;
        $clone->noCache = false;
        return $clone;
    }

    public function withLastModified(int|string|\DateTime $lastModified): static
    {
        $clone = clone $this;
        $clone->lastModified = toTimestamp($lastModified);
        return $clone;
    }

    public function withoutLastModified(): static
    {
        $clone = clone $this;
        $clone->lastModified = null;
        return $clone;
    }

    public function withAge(int $ageSeconds): static
    {
        $clone = clone $this;
        $clone->age = $ageSeconds;
        $clone->noCache = false;
        return $clone;
    }

    public function withoutAge(): static
    {
        $clone = clone $this;
        $clone->age = null;
        return $clone;
    }

    public function withMaxAge(int $seconds = 0,
                               int $minutes = 0,
                               int $hours = 0,
                               int $days = 0,
                               int $weeks = 0,
                               int $months = 0,
                               int $years = 0): static
    {
        $clone = clone $this;
        $clone->maxAge = durationToSeconds(
            $seconds,
            $minutes,
            $hours,
            $days,
            $weeks,
            $months,
            $years
        );
        $clone->noCache = false;
        return $clone;
    }

    public function withoutMaxAge(): static
    {
        $clone = clone $this;
        $clone->maxAge = null;
        return $clone;
    }

    public function withSharedMaxAge(int $seconds = 0,
                                     int $minutes = 0,
                                     int $hours = 0,
                                     int $days = 0,
                                     int $weeks = 0,
                                     int $months = 0,
                                     int $years = 0): static
    {
        $clone = clone $this;
        $clone->sharedMaxAge = durationToSeconds(
            $seconds,
            $minutes,
            $hours,
            $days,
            $weeks,
            $months,
            $years
        );
        $clone->noCache = false;
        return $clone;
    }

    public function withoutSharedMaxAge(): static
    {
        $clone = clone $this;
        $clone->sharedMaxAge = null;
        return $clone;
    }

    public function withMustRevalidate(): static
    {
        $clone = clone $this;
        $clone->mustRevalidate = true;
        $clone->noCache = false;
        return $clone;
    }

    public function withProxyRevalidate(): static
    {
        $clone = clone $this;
        $clone->proxyRevalidate = true;
        $clone->noCache = false;
        return $clone;
    }

    public function withNoStore(): static
    {
        $clone = clone $this;
        $clone->noStore = true;
        $clone->noCache = false;
        return $clone;
    }

    public function withPrivate(): static
    {
        $clone = clone $this;
        $clone->private = true;
        $clone->noCache = false;
        return $clone;
    }

    public function withPublic(): static
    {
        $clone = clone $this;
        $clone->public = true;
        $clone->noCache = false;
        return $clone;
    }

    public function withMustUnderstand(): static
    {
        $clone = clone $this;
        $clone->mustUnderstand = true;
        $clone->noCache = false;
        return $clone;
    }

    public function withNoTransform(): static
    {
        $clone = clone $this;
        $clone->noTransform = true;
        $clone->noCache = false;
        return $clone;
    }

    public function withImmutable(): static
    {
        $clone = clone $this;
        $clone->immutable = true;
        $clone->noCache = false;
        return $clone;
    }

    public function withStaleWhileRevalidate(int $seconds = 0,
                                             int $minutes = 0,
                                             int $hours = 0,
                                             int $days = 0,
                                             int $weeks = 0,
                                             int $months = 0,
                                             int $years = 0): static
    {
        $clone = clone $this;
        $clone->staleWhileRevalidate = durationToSeconds(
            $seconds,
            $minutes,
            $hours,
            $days,
            $weeks,
            $months,
            $years
        );
        $clone->noCache = false;
        return $clone;
    }

    public function withoutStaleWhileRevalidate(): static
    {
        $clone = clone $this;
        $clone->staleWhileRevalidate = null;
        return $clone;
    }

    public function withStaleIfError(int $seconds = 0,
                                     int $minutes = 0,
                                     int $hours = 0,
                                     int $days = 0,
                                     int $weeks = 0,
                                     int $months = 0,
                                     int $years = 0): static
    {
        $clone = clone $this;
        $clone->staleIfError = durationToSeconds(
            $seconds,
            $minutes,
            $hours,
            $days,
            $weeks,
            $months,
            $years
        );
        $clone->noCache = false;
        return $clone;
    }

    public function withoutStaleIfError(): static
    {
        $clone = clone $this;
        $clone->staleIfError = null;
        return $clone;
    }

    public function withETag(ETagHeaderBuilder $ETagHeaderBuilder): static
    {
        $clone = clone $this;
        $clone->ETagHeaderBuilder = $ETagHeaderBuilder;
        return $clone;
    }

    public function withoutETag(): static
    {
        $clone = clone $this;
        $clone->ETagHeaderBuilder = null;
        return $clone;
    }

    public function toHeaders(): array
    {
        $headers = [];
        if (null !== $this->lastModified) {
            $headers[self::LAST_MODIFIED_HEADER] = httpHeaderDate($this->lastModified);
        }

        if ($this->noCache) {
            $headers[self::CACHE_CONTROL_HEADER] = 'no-cache';
            return $headers;
        }
        // no-cache disables all other cache headers

        if (null !== $this->expires) {
            $headers[self::EXPIRES_HEADER] = httpHeaderDate($this->expires);
        }
        $cacheControl = [];
        if ($this->maxAge !== null) {
            $cacheControl[] = sprintf("max-age=%d", $this->maxAge);
        }
        if ($this->sharedMaxAge !== null) {
            $cacheControl[] = sprintf("s-maxage=%d", $this->sharedMaxAge);
        }

        if ($this->mustRevalidate) {
            $cacheControl[] = 'must-revalidate';
        }

        if ($this->proxyRevalidate) {
            $cacheControl[] = 'proxy-revalidate';
        }

        if ($this->noStore) {
            $cacheControl[] = 'no-store';
        }

        if ($this->private) {
            $cacheControl[] = 'private';
        }

        if ($this->public) {
            $cacheControl[] = 'public';
        }

        if ($this->mustUnderstand) {
            $cacheControl[] = 'must-understand';
        }

        if ($this->immutable) {
            $cacheControl[] = 'immutable';
        }

        if ($this->noTransform) {
            $cacheControl[] = 'no-transform';
        }

        if (null !== $this->staleWhileRevalidate) {
            $cacheControl[] = sprintf("stale-while-revalidate=%d", $this->staleWhileRevalidate);
        }

        if (null !== $this->staleIfError) {
            $cacheControl[] = sprintf("stale-if-error=%d", $this->staleIfError);
        }

        if ([] !== $cacheControl) {
            $headers[self::CACHE_CONTROL_HEADER] = implode(', ', $cacheControl);
        }

        if ($this->age !== null) {
            $headers[self::AGE_HEADER] = (string)$this->age;
        }

        if ($this->ETagHeaderBuilder !== null) {
            $headers = array_merge($headers, $this->ETagHeaderBuilder->toHeaders());
        }

        return $headers;
    }
}