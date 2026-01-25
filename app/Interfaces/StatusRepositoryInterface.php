<?php

namespace App\Interfaces;

interface StatusRepositoryInterface
{
    public function getAllStatuses();
    public function findStatusById($id);
    public function createStatus(array $data);
    public function updateStatus($id, array $data);
    public function deleteStatus($id);
}
