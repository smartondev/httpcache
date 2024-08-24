<?php

namespace SmartonDev\HttpCache\Matchers;

use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;

class MatcherHeaderAbstract
{
    protected array $headers = [];

    public function headers(array $headers): static
    {
        $this->headers = HttpHeaderHelper::replaceHeaders([], $headers);
        return $this;
    }

    public function withHeaders(array $headers): static
    {
        return (clone $this)->headers($headers);
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
}