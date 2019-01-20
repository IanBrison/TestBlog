<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Status;

interface StatusRepository {

    public function insert($user_id, $body): Status;

    public function fetchAllPersonalArchivesByUser(User $user): array;

    public function fetchAllByUser(User $user): array;

    public function fetchById($id): Status;
}
