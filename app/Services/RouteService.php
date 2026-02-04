<?php

namespace App\Services;

use App\Interfaces\RouterInterface;
use App\Helpers\Waypoints;
use Illuminate\Support\Facades\Gate;

class RouteService
{
    protected RouterInterface $repo;

    public function __construct(RouterInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get all routes.
     *
     * Returns an Eloquent collection so it can be used in views and
     * passed directly to JSON responses.
     */
    public function list()
    {
        return $this->repo->getAll();
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function create(array $data)
    {
        Gate::authorize('manage-routes');
        if (isset($data['waypoints'])) {
            $data['waypoints'] = Waypoints::normalize($data['waypoints']);
        }

        return $this->repo->store($data);
    }

    public function delete($id): bool
    {
        Gate::authorize('manage-routes');
        return (bool) $this->repo->delete($id);
    }
}
