<?php

namespace App\Models;

interface User {

    public function id(): int;

    public function name(): string;

    public function isSelf(): bool;

    public function isGuest(): bool;

    public function statuses(): array;

    public function personalStatuses(): array;

    public function followings(): array;

    public function followers(): array;
}
