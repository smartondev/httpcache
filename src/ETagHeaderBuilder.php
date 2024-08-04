<?php

namespace SmartonDev\HttpCache;

class ETagHeaderBuilder implements HttpHeaderInterface
{
    public const ETAG_HEADER = 'etag';

    private mixed $etag = null;

    private bool $weekETag = false;

    public function withETag(mixed $etag, bool $isWeek = false): static
    {
        $clone = clone $this;
        $clone->etag = $etag;
        return $clone->withIsWeekEtag($isWeek);
    }

    public function withComputedEtag($data, callable $func): static
    {
        $etag = call_user_func($func, $data);
        return $this->withETag($etag);
    }

    public function withIsWeekEtag(bool $week = true): static
    {
        $clone = clone $this;
        $clone->weekETag = $week;
        return $clone;
    }

    public function toHeaders(): array
    {
        if ($this->isEmpty()) {
            return [];
        }
        return [
            self::ETAG_HEADER => $this->getETagHeaderValue()
        ];
    }

    public function getETagHeaderValue(): ?string
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
        return $this->getETagHeaderValue() ?? '';
    }
}