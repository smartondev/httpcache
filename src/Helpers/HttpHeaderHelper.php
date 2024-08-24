<?php

namespace SmartonDev\HttpCache\Helpers;

class HttpHeaderHelper
{

    public static function toDateString(int $timestamp): string
    {
        return gmdate('D, d M Y H:i:s \G\M\T', $timestamp);
    }

    public static function isValidDateString(string $date): bool
    {
        $ts = strtotime($date);
        if (false === $ts) {
            return false;
        }
        return HttpHeaderHelper::toDateString($ts) === $date;
    }

    public static function getFirstHeaderValue(array $headers, string $name): ?string
    {
        $name = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower($key) === $name) {
                return is_array($value) ? reset($value) : $value;
            }
        }
        return null;
    }

    public static function replaceHeaders(array $headers, array $replaceHeaders): array
    {
        $output = [];
        foreach ($headers as $key => $value) {
            $output[strtolower($key)] = $value;
        }
        foreach ($replaceHeaders as $key => $value) {
            $output[strtolower($key)] = $value;
        }
        return $output;
    }
}