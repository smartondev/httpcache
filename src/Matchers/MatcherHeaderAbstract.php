<?php

declare(strict_types=1);
namespace SmartonDev\HttpCache\Matchers;

use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;

class MatcherHeaderAbstract
{
    /**
     * @var array<string, string|array<string>>
     */
    protected array $headers = [];

    /**
     * @param array<string, string|array<string>> $headers
     */
    public function headers(array $headers): static
    {
        $this->headers = HttpHeaderHelper::replaceHeaders([], $headers);
        return $this;
    }

    /**
     * @param array<string, string|array<string>> $headers
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
}