<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Status;

class RepairBusinessService
{
    protected $serviceRepo;

    public function __construct(ServiceInterface $serviceRepo)
    {
        $this->serviceRepo = $serviceRepo;
    }

    // Status "New" ကို ရှာပေးတဲ့ Logic
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

    // အခြား လိုအပ်တဲ့ Logic တွေကို Repo ဆီ တိုက်ရိုက်လှမ်းခေါ်မယ်
    public function getServiceList()
    {
        return $this->serviceRepo->getAllServices();
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
