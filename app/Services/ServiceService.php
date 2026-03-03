<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Status;
use Illuminate\Support\Facades\Gate;

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

        // Debug ၁: Status ရှိမရှိအရင်စစ်ပါ
        if (!$status) {
            dd('Default status record database ထဲမှာ မရှိသေးပါဘူး။');
        }

        $data['status_id'] = $status->id;

        // Debug ၂: Repo ထဲမသွင်းခင် Data ကို စစ်ပါ
        // dd($data); 

        $services = $this->serviceRepo->storeService($data);
        // dd($services);
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
        Gate::authorize('manage-services');
        return $this->serviceRepo->updateService($id, $data);
    }

    public function deleteRecord($id)
    {
        Gate::authorize('manage-services');
        return $this->serviceRepo->deleteService($id);
    }
}
