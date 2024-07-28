<?php

namespace SmartonDev\HttpCache;

function durationToSeconds(int $seconds = 0,
                           int $minutes = 0,
                           int $hours = 0,
                           int $days = 0,
                           int $weeks = 0,
                           int $months = 0,
                           int $years = 0): int
{
    return $seconds
        + $minutes * 60
        + $hours * 3600
        + $days * 86400
        + $weeks * 86400 * 7
        + $months * 86400 * 30
        + $years * 86400 * 365;
}

function httpHeaderDate(int $timestamp): string
{
    return gmdate('D, d M Y H:i:s \G\M\T', $timestamp);
}

function isValidHttpHeaderDate(string $date): bool
{
    $ts = strtotime($date);
    if (false === $ts) {
        return false;
    }
    return httpHeaderDate($ts) === $date;
}

function getHeaderFirstValue(array $headers, string $name): ?string
{
    $name = strtolower($name);
    foreach ($headers as $key => $value) {
        if (strtolower($key) === $name) {
            return is_array($value) ? reset($value) : $value;
        }
    }
    return null;
}

function toTimestamp(int|string|\DateTime $input): int
{
    if (is_int($input)) {
        return $input;
    }
    if (!($input instanceof \DateTime)) {
        $input = new \DateTime($input);
    }
    return $input->getTimestamp();
}