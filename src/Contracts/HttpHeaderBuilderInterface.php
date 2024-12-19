<?php

namespace SmartonDev\HttpCache\Contracts;

interface HttpHeaderBuilderInterface
{
    /**
     * Convert to headers array
     * @return array<string, string>
     */
    public function toHeaders(): array;
}