<?php

namespace App\Models\ValueObject;

use App\Models\TimeStamp;

class NormalTimeStamp implements TimeStamp {

    private $unixTime;

    public function __construct(int $unixTime) {
        $this->unixTime = $unixTime;
    }

    public static function constructFromUnixTime(int $unixTime): TimeStamp {
        return new Self($dateString);
    }

    public static function constructFromString(string $dateString): TimeStamp {
        return new Self(strtotime($dateString));
    }

    public function showDate(): string {
        return date('Y-m-d', $this->unixTime);
    }

    public function showDateAndTime(): string {
        return date('Y-m-d H:i:s', $this->unixTime);
    }
}
