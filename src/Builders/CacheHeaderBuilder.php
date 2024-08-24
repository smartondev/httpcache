<?php

namespace SmartonDev\HttpCache\Builders;

use SmartonDev\HttpCache\Contracts\HttpHeaderBuilderInterface;
use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;
use SmartonDev\HttpCache\Helpers\TimeHelper;

class CacheHeaderBuilder implements HttpHeaderBuilderInterface
{
    private const AGE_HEADER = 'age';
    private const CACHE_CONTROL_HEADER = 'cache-control';
    private const PRAGMA_HEADER = 'pragma';
    private const LAST_MODIFIED_HEADER = 'last-modified';
    private const EXPIRES_HEADER = 'expires';
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

    private null|string $etag = null;

    public function noCache(): static
    {
        $this->reset();
        $this->noCache = true;
        return $this;
    }

    public function withNoCache(): static
    {
        return (clone $this)->noCache();
    }

    public function reset(): static
    {
        $this->noCache = false;
        $this->maxAge = null;
        $this->sharedMaxAge = null;
        $this->mustRevalidate = false;
        $this->proxyRevalidate = false;
        $this->noStore = false;
        $this->private = false;
        $this->public = false;
        $this->mustUnderstand = false;
        $this->noTransform = false;
        $this->immutable = false;
        $this->staleWhileRevalidate = null;
        $this->staleIfError = null;
        $this->age = null;
        $this->etag = null;
        $this->expires = null;
        $this->lastModified = null;
        return $this;
    }

    public function withReset(): static
    {
        return (clone $this)->reset();
    }

    private function resetIfNoCache(): static
    {
        if ($this->noCache) {
            return $this->reset();
        }
        return $this;
    }

    public function expires(int|string|\Datetime $expires): static
    {
        $this->resetIfNoCache();
        $this->expires = TimeHelper::toTimestamp($expires);
        return $this;
    }

    public function withExpires(int|string|\DateTime $expires): static
    {
        return (clone $this)
            ->expires($expires);
    }

    public function resetExpires(): static
    {
        $this->expires = null;
        return $this;
    }

    public function withoutExpires(): static
    {
        return (clone $this)
            ->resetExpires();
    }

    public function lastModified(int|string|\DateTime $lastModified): static
    {
        $this->resetIfNoCache();
        $this->lastModified = TimeHelper::toTimestamp($lastModified);
        return $this;
    }

    public function withLastModified(int|string|\DateTime $lastModified): static
    {
        return (clone $this)
            ->lastModified($lastModified);
    }

    public function resetLastModified(): static
    {
        $this->lastModified = null;
        return $this;
    }

    public function withoutLastModified(): static
    {
        return (clone $this)
            ->resetLastModified();
    }

    public function age(int $ageSeconds): static
    {
        $this->resetIfNoCache();
        $this->age = $ageSeconds;
        return $this;
    }

    public function withAge(int $ageSeconds): static
    {
        return (clone $this)
            ->age($ageSeconds);
    }

    public function resetAge(): static
    {
        $this->age = null;
        return $this;
    }

    public function withoutAge(): static
    {
        return (clone $this)
            ->resetAge();
    }

    public function maxAge(int $seconds = 0,
                           int $minutes = 0,
                           int $hours = 0,
                           int $days = 0,
                           int $weeks = 0,
                           int $months = 0,
                           int $years = 0): static
    {
        $this->resetIfNoCache();
        $this->maxAge = TimeHelper::durationToSeconds(
            $seconds,
            $minutes,
            $hours,
            $days,
            $weeks,
            $months,
            $years
        );
        return $this;
    }

    public function withMaxAge(int $seconds = 0,
                               int $minutes = 0,
                               int $hours = 0,
                               int $days = 0,
                               int $weeks = 0,
                               int $months = 0,
                               int $years = 0): static
    {
        return (clone $this)
            ->maxAge($seconds, $minutes, $hours, $days, $weeks, $months, $years);
    }

    public function resetMaxAge(): static
    {
        $this->maxAge = null;
        return $this;
    }

    public function withoutMaxAge(): static
    {
        return (clone $this)
            ->resetMaxAge();
    }

    public function sharedMaxAge(int $seconds = 0,
                                 int $minutes = 0,
                                 int $hours = 0,
                                 int $days = 0,
                                 int $weeks = 0,
                                 int $months = 0,
                                 int $years = 0): static
    {
        $this->resetIfNoCache();
        $this->sharedMaxAge = TimeHelper::durationToSeconds(
            $seconds,
            $minutes,
            $hours,
            $days,
            $weeks,
            $months,
            $years
        );
        return $this;
    }

    public function withSharedMaxAge(int $seconds = 0,
                                     int $minutes = 0,
                                     int $hours = 0,
                                     int $days = 0,
                                     int $weeks = 0,
                                     int $months = 0,
                                     int $years = 0): static
    {
        return (clone $this)
            ->sharedMaxAge($seconds, $minutes, $hours, $days, $weeks, $months, $years);
    }

    public function resetSharedMaxAge(): static
    {
        $this->sharedMaxAge = null;
        return $this;
    }

    public function withoutSharedMaxAge(): static
    {
        return (clone $this)
            ->resetSharedMaxAge();
    }

    public function mustRevalidate(): static
    {
        $this->resetIfNoCache();
        $this->mustRevalidate = true;
        return $this;
    }

    public function withMustRevalidate(): static
    {
        return (clone $this)
            ->mustRevalidate();
    }

    public function resetMustRevalidate(): static
    {
        $this->mustRevalidate = false;
        return $this;
    }

    public function withoutMustRevalidate(): static
    {
        return (clone $this)
            ->resetMustRevalidate();
    }

    public function proxyRevalidate(): static
    {
        $this->resetIfNoCache();
        $this->proxyRevalidate = true;
        return $this;
    }

    public function withProxyRevalidate(): static
    {
        return (clone $this)
            ->proxyRevalidate();
    }

    public function resetProxyRevalidate(): static
    {
        $this->proxyRevalidate = false;
        return $this;
    }

    public function withoutProxyRevalidate(): static
    {
        return (clone $this)
            ->resetProxyRevalidate();
    }

    public function noStore(): static
    {
        $this->resetIfNoCache();
        $this->noStore = true;
        return $this;
    }

    public function withNoStore(): static
    {
        return (clone $this)
            ->noStore();
    }

    public function resetNoStore(): static
    {
        $this->noStore = false;
        return $this;
    }

    public function withoutNoStore(): static
    {
        return (clone $this)
            ->resetNoStore();
    }

    public function private(): static
    {
        $this->resetIfNoCache();
        $this->private = true;
        $this->public = false;
        return $this;
    }

    public function withPrivate(): static
    {
        return (clone $this)
            ->private();
    }

    public function resetPrivate(): static
    {
        $this->private = false;
        return $this;
    }

    public function withoutPrivate(): static
    {
        return (clone $this)
            ->resetPrivate();
    }

    public function public(): static
    {
        $this->resetIfNoCache();
        $this->public = true;
        $this->private = false;
        return $this;
    }

    public function withPublic(): static
    {
        return (clone $this)
            ->public();
    }

    public function resetPublic(): static
    {
        $this->public = false;
        return $this;
    }

    public function withoutPublic(): static
    {
        return (clone $this)
            ->resetPublic();
    }

    public function mustUnderstand(): static
    {
        $this->resetIfNoCache();
        $this->mustUnderstand = true;
        return $this;
    }

    public function withMustUnderstand(): static
    {
        return (clone $this)
            ->mustUnderstand();
    }

    public function resetMustUnderstand(): static
    {
        $this->mustUnderstand = false;
        return $this;
    }

    public function withoutMustUnderstand(): static
    {
        return (clone $this)
            ->resetMustUnderstand();
    }

    public function noTransform(): static
    {
        $this->noTransform = true;
        return $this;
    }

    public function withNoTransform(): static
    {
        return (clone $this)
            ->noTransform();
    }

    public function resetNoTransform(): static
    {
        $this->noTransform = false;
        return $this;
    }

    public function withoutNoTransform(): static
    {
        return (clone $this)
            ->resetNoTransform();
    }

    public function immutable(): static
    {
        $this->immutable = true;
        return $this;
    }

    public function withImmutable(): static
    {
        return (clone $this)
            ->immutable();
    }

    public function resetImmutable(): static
    {
        $this->immutable = false;
        return $this;
    }

    public function withoutImmutable(): static
    {
        return (clone $this)
            ->resetImmutable();
    }

    public function staleWhileRevalidate(int $seconds = 0,
                                         int $minutes = 0,
                                         int $hours = 0,
                                         int $days = 0,
                                         int $weeks = 0,
                                         int $months = 0,
                                         int $years = 0): static
    {
        $this->resetIfNoCache();
        $this->staleWhileRevalidate = TimeHelper::durationToSeconds(
            $seconds,
            $minutes,
            $hours,
            $days,
            $weeks,
            $months,
            $years
        );
        return $this;
    }

    public function withStaleWhileRevalidate(int $seconds = 0,
                                             int $minutes = 0,
                                             int $hours = 0,
                                             int $days = 0,
                                             int $weeks = 0,
                                             int $months = 0,
                                             int $years = 0): static
    {
        return (clone $this)
            ->staleWhileRevalidate($seconds, $minutes, $hours, $days, $weeks, $months, $years);
    }

    public function resetStaleWhileRevalidate(): static
    {
        $this->staleWhileRevalidate = null;
        return $this;
    }

    public function withoutStaleWhileRevalidate(): static
    {
        return (clone $this)
            ->resetStaleWhileRevalidate();
    }

    public function staleIfError(int $seconds = 0,
                                 int $minutes = 0,
                                 int $hours = 0,
                                 int $days = 0,
                                 int $weeks = 0,
                                 int $months = 0,
                                 int $years = 0): static
    {
        $this->resetIfNoCache();
        $this->staleIfError = TimeHelper::durationToSeconds(
            $seconds,
            $minutes,
            $hours,
            $days,
            $weeks,
            $months,
            $years
        );
        return $this;
    }

    public function withStaleIfError(int $seconds = 0,
                                     int $minutes = 0,
                                     int $hours = 0,
                                     int $days = 0,
                                     int $weeks = 0,
                                     int $months = 0,
                                     int $years = 0): static
    {
        return (clone $this)
            ->staleIfError($seconds, $minutes, $hours, $days, $weeks, $months, $years);
    }

    public function resetStaleIfError(): static
    {
        $this->staleIfError = null;
        return $this;
    }

    public function withoutStaleIfError(): static
    {
        return (clone $this)
            ->resetStaleIfError();
    }

    /**
     * Set ETAG.
     *
     * If ETAG is empty, it will be reset.
     *
     * @param string|ETagHeaderBuilder $etag
     * @return $this
     */
    public function etag(string|ETagHeaderBuilder $etag): static
    {
        $this->resetIfNoCache();
        if ($etag instanceof ETagHeaderBuilder) {
            $etag = $etag->getETag();
        }
        if (is_string($etag) && trim($etag) === '') {
            $etag = null;
        }
        $this->etag = $etag;
        return $this;
    }

    /**
     * New instance with ETAG.
     *
     * If ETAG is empty, it will be reset.
     *
     * @param string|ETagHeaderBuilder $etag
     * @return $this
     */
    public function withETag(string|ETagHeaderBuilder $etag): static
    {
        return (clone $this)
            ->etag($etag);
    }

    public function resetETag(): static
    {
        $this->etag = null;
        return $this;
    }

    public function withoutETag(): static
    {
        return (clone $this)
            ->resetETag();
    }

    public function toHeaders(): array
    {
        $headers = [];
        if (null !== $this->lastModified) {
            $headers[self::LAST_MODIFIED_HEADER] = HttpHeaderHelper::toDateString($this->lastModified);
        }
        if ($this->noCache) {
            $cacheControl = [
                'must-revalidate',
                'no-store',
                'private',
                'no-cache',
            ];
            sort($cacheControl);
            $headers[self::CACHE_CONTROL_HEADER] = join(', ', $cacheControl);
            $headers[self::PRAGMA_HEADER] = 'no-cache';
            return $headers;
        }

        $cacheControl = [];
        if ($this->mustRevalidate) {
            $cacheControl[] = 'must-revalidate';
        }

        if ($this->noStore) {
            $cacheControl[] = 'no-store';
        }

        if ($this->private) {
            $cacheControl[] = 'private';
        }
        // no-cache disables all other cache headers

        if (null !== $this->expires) {
            $headers[self::EXPIRES_HEADER] = HttpHeaderHelper::toDateString($this->expires);
        }
        if ($this->maxAge !== null) {
            $cacheControl[] = sprintf("max-age=%d", $this->maxAge);
        }
        if ($this->sharedMaxAge !== null) {
            $cacheControl[] = sprintf("s-maxage=%d", $this->sharedMaxAge);
        }

        if ($this->proxyRevalidate) {
            $cacheControl[] = 'proxy-revalidate';
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
            sort($cacheControl);
            $headers[self::CACHE_CONTROL_HEADER] = implode(', ', $cacheControl);
        }

        if ($this->age !== null) {
            $headers[self::AGE_HEADER] = (string)$this->age;
        }

        if ($this->hasETag()) {
            $headers[ETagHeaderBuilder::ETAG_HEADER] = $this->etag;
        }

        return $headers;
    }

    public function isNoCache(): bool
    {
        return $this->noCache;
    }

    public function isEmpty(): bool
    {
        return [] === $this->toHeaders();
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function hasETag(): bool
    {
        return null !== $this->etag;
    }

    public function getETag(): ?string
    {
        return $this->etag;
    }

    public function hasLastModified(): bool
    {
        return null !== $this->lastModified;
    }
}