<?php

namespace SmartonDev\HttpCache\Tests\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SmartonDev\HttpCache\Helpers\HttpHeaderHelper;
use SmartonDev\HttpCache\Helpers\TimeHelper;

class TimeHelperTest extends TestCase
{

    public function testDurationToSeconds(): void
    {
        $this->assertSame(37, TimeHelper::durationToSeconds(37));
        $this->assertSame(3600, TimeHelper::durationToSeconds(hours: 1));
        $this->assertSame(1800, TimeHelper::durationToSeconds(minutes: 30));
        $this->assertSame(60, TimeHelper::durationToSeconds(seconds: 60));
        $this->assertSame(86400, TimeHelper::durationToSeconds(days: 1));
        $this->assertSame(604800, TimeHelper::durationToSeconds(weeks: 1));
        $this->assertSame(2592000, TimeHelper::durationToSeconds(months: 1));
        $this->assertSame(31536000, TimeHelper::durationToSeconds(years: 1));
        $this->assertSame(3722, TimeHelper::durationToSeconds(seconds: 2, minutes: 2, hours: 1));
    }
}