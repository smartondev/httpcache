<?php

declare(strict_types=1);
namespace SmartonDev\HttpCache\Builders;

use SmartonDev\HttpCache\Contracts\HttpHeaderBuilderInterface;

class ETagHeaderBuilder implements HttpHeaderBuilderInterface
{
    public const ETAG_HEADER = 'etag';

    private ?string $etag = null;

    private bool $weekETag = false;

    /**
     * Set ETag header
     *
     * @note If $etag is empty, it will be set to null
     * @note If $etag is null, weekETag will be set to false and $isWeek will be ignored
     */
    public function etag(null|string $etag, bool $isWeek = false): static
    {
        if (is_string($etag) && trim($etag) === '') {
            $etag = null;
        }
        $this->etag = $etag;
        if(null === $etag) {
            return $this->weekETag(false);
        }
        return $this->weekETag($isWeek);
    }

    /**
     * Set ETag header from computed value
     *
     * If $etag is empty, it will be set to null
     */
    public function computedETag(mixed $data, callable $func, bool $week = false): static
    {
        $etag = call_user_func($func, $data);
        if(!is_string($etag) && null !== $etag) {
            throw new \InvalidArgumentException('ETag must be a string or null');
        }
        return $this->etag($etag, $week);
    }

    /**
     * New instance with ETag header
     *
     * If $etag is empty, it will be set to null
     */
    public function withComputedETag(mixed $data, callable $func, bool $week = false): static
    {
        return (clone $this)->computedETag($data, $func, $week);
    }

    /**
     * New instance with ETag header
     *
     * If $etag is empty, it will be set to null
     */
    public function withEtag(?string $etag): static
    {
        return (clone $this)->etag($etag);
    }

    /**
     * Reset ETag header
     */
    public function resetETag(): static
    {
        $this->etag = null;
        return $this;
    }

    /**
     * New instance without ETag header
     */
    public function withoutETag(): static
    {
        return (clone $this)->resetETag();
    }

    /**
     * Set ETag header to weak
     */
    public function weekETag(bool $week = true): static
    {
        $this->weekETag = $week;
        return $this;
    }

    /**
     * New instance with weak ETag header
     */
    public function withWeekETag(bool $week = true): static
    {
        return (clone $this)->weekETag($week);
    }

    /**
     * Reset ETag header to strong
     */
    public function resetWeekETag(): static
    {
        $this->weekETag = false;
        return $this;
    }

    /**
     * New instance without weak ETag header
     */
    public function withoutWeekETag(): static
    {
        return (clone $this)->resetWeekETag();
    }

    public function toHeaders(): array
    {
        if ($this->isEmpty()) {
            return [];
        }
        $etag = $this->getETag();
        if(null === $etag) {
            throw new \LogicException('ETag is empty');
        }
        return [
            self::ETAG_HEADER => $etag,
        ];
    }

    public function getETag(): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }
        $etag = sprintf('"%s"', $this->etag);
        if (!$this->weekETag) {
            return $etag;
        }
        return sprintf('W/%s', $etag);
    }

    public function isEmpty(): bool
    {
        return null === $this->etag;
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function __toString(): string
    {
        return $this->getETag() ?? '';
    }
}