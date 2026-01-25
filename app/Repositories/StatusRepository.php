<?php

namespace App\Repositories;

use App\Interfaces\StatusRepositoryInterface;
use App\Models\Status;

class StatusRepository implements StatusRepositoryInterface
{
    protected $model;

    public function __construct(Status $model)
    {
        $this->model = $model;
    }

    public function getAllStatuses()
    {
        return $this->model->all();
    }

    public function findStatusById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function createStatus(array $data)
    {
        return $this->model->create($data);
    }

    public function updateStatus($id, array $data)
    {
        $item = $this->model->findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function deleteStatus($id)
    {
        $item = $this->model->findOrFail($id);
        return $item->delete();
    }
}
