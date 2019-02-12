<?php

namespace App\Repositories\Dao;

use Core\Di\DiContainer as Di;
use Core\Datasource\DbDao;
use Core\Storage\Storage;
use Core\Storage\File;
use App\Models\User;
use App\Models\Status;
use App\Models\Entity\Status\GhostStatus;
use App\Models\Image as ImageModel;
use App\Models\Entity\Image\Image;
use App\Repositories\ImageRepository;
use \Exception;

class ImageDbDao extends DbDao implements ImageRepository {

    public function insert(Status $status, File $file): ImageModel {
        $time = time();
        $filename = "image_{$time}";
        if (!Di::get(Storage::class)->location('image')->save($file, $filename)) {
            throw new Exception('Something occured during the saving process of Image');
        }

        $sql = "
            INSERT INTO images(filename, status_id)
                VALUES(:filename, :status_id)
        ";

        $this->execute($sql, array(
            ':filename' => $filename,
            ':status_id' => $status->id(),
        ));

        $id = $this->getLastInsertId();
        return new Image($id, $filename, $status);
    }

    public function fetchAllByStatus(Status $status): array {
        $sql = "
            SELECT *
                FROM images
                WHERE status_id = :status_id
        ";

        $rows = $this->fetchAll($sql, array(':status_id' => $status->id()));

        $images = array();
        foreach ($rows as $row) {
            $images[] = new Image($row['id'], Di::get(Storage::class)->location('image')->url($row['filename']), $status);
        }
        return $images;
    }

    public function fetchAllByUser(User $user): array {
        $sql = "
            SELECT images.*
                FROM images
                LEFT JOIN status ON images.status_id = status.id
                WHERE status.user_id = :user_id
        ";

        $rows = $this->fetchAll($sql, array(':user_id' => $user->id()));

        $images = array();
        foreach ($rows as $row) {
            $images[] = new Image($row['id'], $row['filename'], new GhostStatus($row['status_id']));
        }
        return $images;
    }
}
