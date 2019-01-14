<?php

namespace App\Repositories;

use App\Models\User;

interface FollowRepository {

    public function follow(User $user, User $user_to_be_followed): bool;

    public function getFollowings(User $user): array;

    public function getFollowers(User $user): array;

    public function isFollowing(User $user, User $user_might_followed): bool;
}
