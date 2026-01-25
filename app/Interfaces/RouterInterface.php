<?php
namespace App\Interfaces;
interface RouterInterface
{
    public function getAll();
    public function find($id);
    public function store(array $data);
    public function delete($id);
}
