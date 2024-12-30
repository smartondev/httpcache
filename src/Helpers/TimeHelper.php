<?php

declare(strict_types=1);
namespace SmartonDev\HttpCache\Helpers;

use DateTime;
use SmartonDev\HttpCache\Exceptions\DateMalformedStringException;

class TimeHelper
{
    /**
     * @param int $seconds
     * @param int $minutes
     * @param int $hours
     * @param int $days
     * @param int $weeks
     * @param int $months calculated as 30 days
     * @param int $years calculated as 365 days
     * @return int calculated seconds
     *
     * @example TimeHelper::durationToSeconds(37) // 37 seconds
     * @example TimeHelper::durationToSeconds(hours: 1) // 1 hour
     * @example TimeHelper::durationToSeconds(minutes: 30) // 30 minutes
     * @example TimeHelper::durationToSeconds(seconds: 13, minutes: 2, hours: 1) // 1 hour, 2 minutes, 13 seconds
     */
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
        if (!is_string($input)) {
            throw new \LogicException('Input must be int, string or DateTime object');
        }
        if (trim($input) === '') {
            throw new DateMalformedStringException('Empty string');
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