<?php

declare(strict_types=1);
namespace SmartonDev\HttpCache\Helpers;

use DateTime;
use SmartonDev\HttpCache\Exceptions\DateMalformedStringException;

class TimeHelper
{
    public static function durationToSeconds(int $seconds = 0,
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

    /**
     * @param int|string|DateTime $input int timestamp, string date (DateTime input) or DateTime object
     * @return int
     * @throws DateMalformedStringException
     */
    public static function toTimestamp(int|string|DateTime $input): int
    {
        if (is_int($input)) {
            return $input;
        }
        if ($input instanceof DateTime) {
            return $input->getTimestamp();
        }
        try {
            $input = new DateTime($input);
        } catch (\Exception $e) {
            // before php8.3 \DateMalformedStringException is not available
            throw new DateMalformedStringException(
                message: $e->getMessage(),
                code: $e->getCode(),
                previous: $e,
            );
        }
        return $input->getTimestamp();
    }
}