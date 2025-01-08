<?php

declare(strict_types=1);

namespace SmartonDev\HttpCache\Matchers;

use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;

class MatcherHeaderAbstract
{
    /**
     * @var array<string, string|array<string>>
     */
    private array $headers = [];

    /**
     * @param array<string, string|array<string>> $headers
     *
     * @note header names are converted to lowercase
     */
    public function headers(array $headers): static
    {
        $this->headers = HttpHeaderHelper::replaceHeaders([], $headers);
        return $this;
    }

    /**
     * @param array<string, string|array<string>> $headers
     *
     * @note header names are converted to lowercase
     */
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

    /**
     * @return array<string, string|array<string>>
     */
    protected function getHeaders(): array
    {
        return $this->headers;
    }
}