<?php

namespace App\Repositories;

interface StatusRepository {

    public function insert($user_id, $body): bool;

    public function fetchAllPersonalArchivesByUserId($user_id): array;
}
