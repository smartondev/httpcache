<?php

declare(strict_types=1);
namespace SmartonDev\HttpCache\Builders;

use Datetime;
use SmartonDev\HttpCache\Contracts\HttpHeaderBuilderInterface;
use SmartonDev\HttpCache\Exceptions\DateMalformedStringException;
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

    /**
     * Set no cache headers.
     *
     * Cache-Control: must-revalidate, no-store, private, no-cache
     * Pragma: no-cache
     */
    public function noCache(): static
    {
        $this->reset();
        $this->noCache = true;
        return $this;
    }

    /**
     * New instance with no cache headers.
     *
     * Cache-Control: must-revalidate, no-store, private, no-cache
     * Pragma: no-cache
     */
    public function withNoCache(): static
    {
        return (clone $this)->noCache();
    }

    /**
     * Reset to default state.
     */
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

    /**
     * New instance with reset to default state.
     */
    public function withReset(): static
    {
        return (clone $this)->reset();
    }

    /**
     * Reset if no cache is set.
     */
    private function resetIfNoCache(): static
    {
        if ($this->noCache) {
            return $this->reset();
        }
        return $this;
    }

    /**
     * Set expires header.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires
     *
     * @param int|string|Datetime $expires int timestamp, string date (DateTime input) or DateTime object
     * @return static
     * @throws DateMalformedStringException
     */
    public function expires(int|string|Datetime $expires): static
    {
        $this->resetIfNoCache();
        $this->expires = TimeHelper::toTimestamp($expires);
        return $this;
    }

    /**
     * New instance with expires header.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires
     *
     * @param int|string|Datetime $expires int timestamp, string date (DateTime input) or DateTime object
     * @return static
     * @throws DateMalformedStringException
     */
    public function withExpires(int|string|DateTime $expires): static
    {
        return (clone $this)
            ->expires($expires);
    }

    /**
     * Reset expires header.
     */
    public function resetExpires(): static
    {
        $this->expires = null;
        return $this;
    }

    /**
     * New instance without expires header.
     */
    public function withoutExpires(): static
    {
        return (clone $this)
            ->resetExpires();
    }

    /**
     * Set last modified header.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Last-Modified
     *
     * @param int|string|Datetime $lastModified int timestamp, string date (DateTime input) or DateTime object
     * @return static
     * @throws DateMalformedStringException
     */
    public function lastModified(int|string|DateTime $lastModified): static
    {
        $this->resetIfNoCache();
        $this->lastModified = TimeHelper::toTimestamp($lastModified);
        return $this;
    }

    /**
     * New instance with last modified header.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Last-Modified
     *
     * @param int|string|Datetime $lastModified int timestamp, string date (DateTime input) or DateTime object
     * @return static
     * @throws DateMalformedStringException
     */
    public function withLastModified(int|string|DateTime $lastModified): static
    {
        return (clone $this)
            ->lastModified($lastModified);
    }

    /**
     * Reset last modified header.
     */
    public function resetLastModified(): static
    {
        $this->lastModified = null;
        return $this;
    }

    /**
     * New instance without last modified header.
     */
    public function withoutLastModified(): static
    {
        return (clone $this)
            ->resetLastModified();
    }

    /**
     * Set age seconds header that contains the age the object has been in a proxy cache.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Age
     */
    public function age(int $ageSeconds): static
    {
        $this->resetIfNoCache();
        $this->age = $ageSeconds;
        return $this;
    }

    /**
     * New instance with age seconds header that contains the age the object has been in a proxy cache.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Age
     */
    public function withAge(int $ageSeconds): static
    {
        return (clone $this)
            ->age($ageSeconds);
    }

    /**
     * Reset age header.
     */
    public function resetAge(): static
    {
        $this->age = null;
        return $this;
    }

    /**
     * New instance without age header.
     */
    public function withoutAge(): static
    {
        return (clone $this)
            ->resetAge();
    }

    /**
     * Set max age that specifies the maximum amount of time a resource will be considered fresh.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
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

    /**
     * New instance with max age that specifies the maximum amount of time a resource will be considered fresh.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
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

    /**
     * Reset max age.
     */
    public function resetMaxAge(): static
    {
        $this->maxAge = null;
        return $this;
    }

    /**
     * New instance without max age.
     */
    public function withoutMaxAge(): static
    {
        return (clone $this)
            ->resetMaxAge();
    }

    /**
     * Set shared max age that specifies the maximum amount of time a resource will be considered fresh by a shared cache.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
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

    /**
     * New instance with shared max age that specifies the maximum amount of time a resource will be considered fresh by a shared cache.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
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

    /**
     * Reset shared max age.
     */
    public function resetSharedMaxAge(): static
    {
        $this->sharedMaxAge = null;
        return $this;
    }

    /**
     * New instance without shared max age.
     */
    public function withoutSharedMaxAge(): static
    {
        return (clone $this)
            ->resetSharedMaxAge();
    }

    /**
     * Set must revalidate that indicates that once a resource becomes stale, a cache must not use the response
     * to satisfy subsequent requests without successful validation on the origin server.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function mustRevalidate(): static
    {
        $this->resetIfNoCache();
        $this->mustRevalidate = true;
        return $this;
    }

    /**
     * New instance with must revalidate that indicates that once a resource becomes stale, a cache must not use the response
     * to satisfy subsequent requests without successful validation on the origin server.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function withMustRevalidate(): static
    {
        return (clone $this)
            ->mustRevalidate();
    }

    /**
     * Reset must revalidate.
     */
    public function resetMustRevalidate(): static
    {
        $this->mustRevalidate = false;
        return $this;
    }

    /**
     * New instance without must revalidate.
     */
    public function withoutMustRevalidate(): static
    {
        return (clone $this)
            ->resetMustRevalidate();
    }

    /**
     * Set proxy revalidate that indicates that once a resource becomes stale, a cache must not use the response
     * to satisfy subsequent requests without successful validation on the origin server.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function proxyRevalidate(): static
    {
        $this->resetIfNoCache();
        $this->proxyRevalidate = true;
        return $this;
    }

    /**
     * New instance with proxy revalidate that indicates that once a resource becomes stale, a cache must not use the response
     * to satisfy subsequent requests without successful validation on the origin server.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function withProxyRevalidate(): static
    {
        return (clone $this)
            ->proxyRevalidate();
    }

    /**
     * Reset proxy revalidate.
     */
    public function resetProxyRevalidate(): static
    {
        $this->proxyRevalidate = false;
        return $this;
    }

    /**
     * New instance without proxy revalidate.
     */
    public function withoutProxyRevalidate(): static
    {
        return (clone $this)
            ->resetProxyRevalidate();
    }

    /**
     * Set no store that indicates that a cache must not store any part of response (private or shared).
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function noStore(): static
    {
        $this->resetIfNoCache();
        $this->noStore = true;
        return $this;
    }

    /**
     * New instance with no store that indicates that a cache must not store any part of response (private or shared).
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function withNoStore(): static
    {
        return (clone $this)
            ->noStore();
    }

    /**
     * Reset no store.
     */
    public function resetNoStore(): static
    {
        $this->noStore = false;
        return $this;
    }

    /**
     * New instance without no store.
     */
    public function withoutNoStore(): static
    {
        return (clone $this)
            ->resetNoStore();
    }

    /**
     * Set private that indicates that a cache must store the response only for a user.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function private(): static
    {
        $this->resetIfNoCache();
        $this->private = true;
        $this->public = false;
        return $this;
    }

    /**
     * New instance with private that indicates that a cache must store the response only for a user.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function withPrivate(): static
    {
        return (clone $this)
            ->private();
    }

    /**
     * Reset private.
     */
    public function resetPrivate(): static
    {
        $this->private = false;
        return $this;
    }

    /**
     * New instance without private.
     */
    public function withoutPrivate(): static
    {
        return (clone $this)
            ->resetPrivate();
    }

    /**
     * Set public that indicates that a cache must store the response for all users.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function public(): static
    {
        $this->resetIfNoCache();
        $this->public = true;
        $this->private = false;
        return $this;
    }

    /**
     * New instance with public that indicates that a cache must store the response for all users.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function withPublic(): static
    {
        return (clone $this)
            ->public();
    }

    /**
     * Reset public.
     */
    public function resetPublic(): static
    {
        $this->public = false;
        return $this;
    }

    /**
     * New instance without public.
     */
    public function withoutPublic(): static
    {
        return (clone $this)
            ->resetPublic();
    }

    /**
     * Set must understand that indicates that a cache must understand the request.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function mustUnderstand(): static
    {
        $this->resetIfNoCache();
        $this->mustUnderstand = true;
        return $this;
    }

    /**
     * New instance with must understand that indicates that a cache must understand the request.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function withMustUnderstand(): static
    {
        return (clone $this)
            ->mustUnderstand();
    }

    /**
     * Reset must understand.
     */
    public function resetMustUnderstand(): static
    {
        $this->mustUnderstand = false;
        return $this;
    }

    /**
     * New instance without must understand.
     */
    public function withoutMustUnderstand(): static
    {
        return (clone $this)
            ->resetMustUnderstand();
    }

    /**
     * Set no transform that indicates that a cache must not transform the response, e.g. no convert images.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function noTransform(): static
    {
        $this->noTransform = true;
        return $this;
    }

    /**
     * New instance with no transform that indicates that a cache must not transform the response, e.g. no convert images.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function withNoTransform(): static
    {
        return (clone $this)
            ->noTransform();
    }

    /**
     * Reset no transform.
     */
    public function resetNoTransform(): static
    {
        $this->noTransform = false;
        return $this;
    }

    /**
     * New instance without no transform.
     */
    public function withoutNoTransform(): static
    {
        return (clone $this)
            ->resetNoTransform();
    }

    /**
     * Set immutable that indicates that response is will not be updated while it's fresh.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function immutable(): static
    {
        $this->immutable = true;
        return $this;
    }

    /**
     * New instance with immutable that indicates that response is will not be updated while it's fresh.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    public function withImmutable(): static
    {
        return (clone $this)
            ->immutable();
    }

    /**
     * Reset immutable.
     */
    public function resetImmutable(): static
    {
        $this->immutable = false;
        return $this;
    }

    /**
     * New instance without immutable.
     */
    public function withoutImmutable(): static
    {
        return (clone $this)
            ->resetImmutable();
    }

    /**
     * Set stale while revalidate that indicates that a cache can serve a stale response while revalidating it.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
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

    /**
     * New instance with stale while revalidate that indicates that a cache can serve a stale response while revalidating it.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
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

    /**
     * Reset stale while revalidate.
     */
    public function resetStaleWhileRevalidate(): static
    {
        $this->staleWhileRevalidate = null;
        return $this;
    }

    /**
     * New instance without stale while revalidate.
     */
    public function withoutStaleWhileRevalidate(): static
    {
        return (clone $this)
            ->resetStaleWhileRevalidate();
    }

    /**
     * Set stale if error that indicates that a cache can serve a stale response if an error occurs.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
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

    /**
     * New instance with stale if error that indicates that a cache can serve a stale response if an error occurs.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
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

    /**
     * Reset stale if error.
     */
    public function resetStaleIfError(): static
    {
        $this->staleIfError = null;
        return $this;
    }

    /**
     * New instance without stale if error.
     */
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

    /**
     * Reset ETAG.
     */
    public function resetETag(): static
    {
        $this->etag = null;
        return $this;
    }

    /**
     * New instance without ETAG.
     */
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
            if(null === $this->etag) {
                throw new \LogicException('ETag is empty');
            }
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