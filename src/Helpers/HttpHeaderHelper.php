<?php

declare(strict_types=1);
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

    /**
     * @param array<string, string|array<string>> $headers
     */
    public static function getFirstHeaderValue(array $headers, string $name): ?string
    {
        $name = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower($key) !== $name) {
                continue;
            }
            if(!is_array($value)) {
                return $value;
            }
            $firstValue = reset($value);
            if(false === $firstValue) {
                return null;
            }
            return $firstValue;
        }
        return null;
    }

    /**
     * @param array<string, string|array<string>> $headers
     * @param array<string, string|array<string>> $replaceHeaders
     * @return array<string, string|array<string>>
     *
     * @note header names are converted to lowercase
     */
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