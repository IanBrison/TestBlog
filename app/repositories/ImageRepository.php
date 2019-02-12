<?php

namespace App\Repositories;

use Core\Storage\File;
use App\Models\User;
use App\Models\Status;
use App\Models\Image;

interface ImageRepository {

    public function insert(Status $status, File $file): Image;

    public function fetchAllByStatus(Status $status): array;

    public function fetchAllByUser(User $user): array;
}
