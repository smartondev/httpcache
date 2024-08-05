<?php

namespace SmartonDev\HttpCache;

interface HttpHeaderInterface
{
    public function toHeaders(): array;
}