<?php

namespace App\Repositories;

use App\Models\Status;

interface StatusRepository {

    public function insert($user_id, $body): Status;

    public function fetchAllPersonalArchivesByUserId($user_id): array;

    public function fetchAllByUserId($user_id): array;

    public function fetchById($id): Status;
}
