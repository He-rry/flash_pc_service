<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Status;

class ServiceService
{
    protected $serviceRepo;

    public function __construct(ServiceInterface $serviceRepo)
    {
        $this->serviceRepo = $serviceRepo;
    }

    private function getDefaultStatus()
    {
        return Status::where('status_name', 'New')->first() ?? Status::where('name', 'New')->first() ?? Status::first();
    }

    public function createReport(array $data)
    {
        $status = $this->getDefaultStatus();
        $data['status_id'] = $status->id;
        return $this->serviceRepo->storeService($data);
    }

    public function getServiceList()
    {
        return $this->serviceRepo->getAllServices();
    }

    public function find($id)
    {
        return $this->serviceRepo->findById($id);
    }

    public function getInitialData()
    {
        return [
            'types' => $this->serviceRepo->getAllServiceTypes(),
            'statuses' => $this->serviceRepo->getAllStatuses(),
        ];
    }

    public function getTrackInfo($phone)
    {
        return $this->serviceRepo->findByPhone($phone);
    }

    public function updateRecord($id, $data)
    {
        return $this->serviceRepo->updateService($id, $data);
    }

    public function deleteRecord($id)
    {
        return $this->serviceRepo->deleteService($id);
    }
}
