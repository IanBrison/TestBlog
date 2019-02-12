<?php

namespace App\Models;

interface Status {

    public function id(): int;

    public function body(): string;

    public function user(): User;

    public function createdAt(): TimeStamp;

    public function isPostedBySelf(): bool;

    public function images(): array;
}
