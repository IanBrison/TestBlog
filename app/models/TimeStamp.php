<?php

namespace App\Models;

interface TimeStamp {

    public function showDate(): string;

    public function showDateAndTime(): string;
}
