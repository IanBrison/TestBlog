<?php

namespace App\Models\Entity\Image;

use App\Models\Status;
use App\Models\User;
use App\Models\Image as ImageInterface;

class Image implements ImageInterface {

    private $id;
    private $filename;
    private $status;

    public function __construct(int $id, string $filename, Status $status) {
        $this->id = $id;
        $this->filename = $filename;
        $this->status = $status;
    }

    public function url(): string {
        return $this->filename;
    }

    public function status(): Status {
        return $this->status;
    }

    public function user(): User {
        return $this->status->user();
    }
}
