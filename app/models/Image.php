<?php

namespace App\Models;

interface Image {

    public function url(): string;

    public function status(): Status;

    public function user(): User;
}
