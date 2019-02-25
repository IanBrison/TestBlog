<?php

namespace App\Services\Usecase;

use Core\Di\DiContainer as Di;
use Core\Storage\File;
use App\Models\User;
use App\Repositories\AuthRepository;
use App\Repositories\StatusRepository;
use App\Repositories\ImageRepository;
use Presentation\Models\Status\Components\StatusPostForm;

class CreateStatus {

    private $errors;

    public function __construct() {
        $this->errors = array();
    }

    public function createFromPost(): bool {
        $statusPostFormViewModel = Di::get(StatusPostForm::class);
        $body = $statusPostFormViewModel->retrieveBody();
        $image = $statusPostFormViewModel->retrieveImage();
        $user = Di::get(AuthRepository::class)->user();
        if ($this->createStatus($user, $body, $image)) {
            return true;
        }

        $statusPostFormViewModel->preserveBody($body);
        return false;
    }

    private function createStatus(User $user, string $body, ?File $image): bool {
        if (!strlen($body)) {
            $this->errors[] = 'ひとことを入力してください';
        } else if (mb_strlen($body) > 200) {
            $this->errors[] = 'ひとことは200文字以内で入力してください';
        }
        if (count($this->errors) > 0) {
            return false;
        }

        $status = Di::get(StatusRepository::class)->insert($user, $body);
        if (!is_null($image)) {
            Di::get(ImageRepository::class)->insert($status, $image);
        }
        return true;
    }

    public function generatePostFormViewModel(): StatusPostForm {
        return Di::get(StatusPostForm::class);
    }

    public function retrieveErrors(): array {
        return $this->errors;
    }
}
