<?php

namespace App\Models;

interface Status {

    public function id(): int;

    public function body(): string;

    public function user(): User;

    public function createdAt(): string;

    public function isPostedBySelf(): bool;
}
