<?php

namespace App\Services;

use App\Interfaces\StatusRepositoryInterface;

class StatusService
{
    protected $repo;

    public function __construct(StatusRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function list()
    {
        return $this->repo->getAllStatuses();
    }

    public function find($id)
    {
        return $this->repo->findStatusById($id);
    }

    public function create(array $data)
    {
        return $this->repo->createStatus($data);
    }

    public function update($id, array $data)
    {
        return $this->repo->updateStatus($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->deleteStatus($id);
    }
}
