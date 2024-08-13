<?php

namespace SmartonDev\HttpCache;

class ETagHeaderBuilder implements HttpHeaderInterface
{
    public const ETAG_HEADER = 'etag';

    private ?string $etag = null;

    private bool $weekETag = false;

    public function etag(null|string $etag, bool $isWeek = false): static
    {
        if (trim($etag) === '') {
            $etag = null;
        }
        $this->etag = $etag;
        return $this->weekETag($isWeek);
    }

    public function computedETag(mixed $data, callable $func, bool $week = false): static
    {
        return $this->etag(call_user_func($func, $data), $week);
    }

    public function withComputedETag(mixed $data, callable $func, bool $week = false): static
    {
        return (clone $this)->computedETag($data, $func, $week);
    }

    public function withEtag(?string $etag): static
    {
        return (clone $this)->etag($etag);
    }

    public function resetETag(): static
    {
        $this->etag = null;
        return $this;
    }

    public function withoutETag(): static
    {
        return (clone $this)->resetETag();
    }

    public function weekETag(bool $week = true): static
    {
        $this->weekETag = $week;
        return $this;
    }

    public function withWeekETag(bool $week = true): static
    {
        return (clone $this)->weekETag($week);
    }

    public function resetETagWeek(): static
    {
        $this->weekETag = false;
        return $this;
    }

    public function withoutWeekETag(): static
    {
        return (clone $this)->resetETagWeek();
    }

    public function toHeaders(): array
    {
        if ($this->isEmpty()) {
            return [];
        }
        return [
            self::ETAG_HEADER => $this->getETag(),
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