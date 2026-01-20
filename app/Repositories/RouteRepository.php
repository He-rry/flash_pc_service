<?php
namespace App\Repositories;
use App\Interfaces\RouterInterface;

class RouteRepository implements RouterInterface
{
    public function getAll()
    {
        return \App\Models\Route::all();
    }
    public function store(array $data)
    {
        return \App\Models\Route::create($data);
    }
    public function delete($id)
    {
        return \App\Models\Route::destroy($id);
    }
}
