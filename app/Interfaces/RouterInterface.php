<?php
namespace App\Interfaces;
interface RouterInterface
{
    public function getAll();
    public function store(array $data);
    public function delete($id);
}
