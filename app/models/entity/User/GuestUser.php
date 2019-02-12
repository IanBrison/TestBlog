<?php

namespace App\Models\Entity\User;

use App\Models\User;

class GuestUser implements User {

    public function id(): int {
        throw new \Exception('No id for guest user');
    }

    public function name(): string {
        return 'ゲスト';
    }

    public function isSelf(): bool {
        return false;
    }

    public function isGuest(): bool {
        return true;
    }

    public function statuses(): array {
        throw new \Exception('No statuses for guest user');
    }

    public function personalStatuses(): array {
        throw new \Exception('No personal statuses for guest user');
    }

    public function followings(): array {
        throw new \Exception('No followings for guest user');
    }

    public function followers(): array {
        throw new \Exception('No followers for guest user');
    }

    public function images(): array {
        throw new \Exception('No images for guest user');
    }
}
