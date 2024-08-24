<?php

namespace SmartonDev\HttpCache\Contracts;

interface HttpHeaderBuilderInterface
{
    public function toHeaders(): array;
}