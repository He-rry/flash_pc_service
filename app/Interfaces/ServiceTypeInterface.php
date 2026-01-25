<?php

namespace App\Interfaces;

interface ServiceTypeInterface {
    public function getAllServiceTypes();
    public function findServiceTypeById($id);
    public function createServiceType(array $data);
    public function updateServiceType($id, array $data);
    public function deleteServiceType($id);
}
