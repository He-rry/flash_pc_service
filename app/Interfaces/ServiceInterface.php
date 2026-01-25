<?php

namespace App\Interfaces;

interface ServiceInterface {
    // Service စာရင်းအားလုံးကို Paginate လုပ်ပြီး ယူရန်
    public function getAllServices();

    // ID တစ်ခုတည်းဖြင့် ရှာရန်
    public function findById($id);
    public function storeService(array $data);

    // ရှိပြီးသား Data ကို ပြင်ရန်
    public function updateService($id, array $data);

    // Data ကို ဖျက်ရန်
    public function deleteService($id);

    // ဖုန်းနံပါတ်ဖြင့် ရှာရန် (Tracking အတွက်)
    public function findByPhone($phone);

    // View မှာ ပြရန် Status များယူရန်
    public function getAllStatuses();

    // View မှာ ပြရန် Service Type များယူရန်
    public function getAllServiceTypes();
}