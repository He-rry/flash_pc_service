<?php
namespace App\Repositories;
use App\Interfaces\RouterInterface;
use App\Models\Route;


class RouteRepository implements RouterInterface
{
    public function getAll()
    {
        return Route::all();
    }
    public function find($id)
    {
        return Route::find($id);
    }
    public function store(array $data)
    {
        return Route::create($data);
    }
    public function delete($id)
    {
        return Route::destroy($id);
    }
}
