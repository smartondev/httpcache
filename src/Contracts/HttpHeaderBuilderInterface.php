<?php

namespace SmartonDev\HttpCache\Contracts;

interface HttpHeaderBuilderInterface
{
    /**
     * Convert to headers array
     */
    public function toHeaders(): array;
}