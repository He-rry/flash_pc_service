<?php

namespace App\Interfaces;

interface ServiceInterface
{
    public function getAllServices();
    public function findById($id);
    public function storeService(array $data);
    public function updateService($id, array $data);
    public function deleteService($id);
    public function findByPhone($phone);
    public function getAllStatuses();
    public function getAllServiceTypes();
}
