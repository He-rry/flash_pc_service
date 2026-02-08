<?php

namespace App\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRepository
{

    public function getAllAdmins()
    {
        return User::with('roles')->latest()->get();
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function store(array $data)
    {
        return User::create($data);
    }
    
    public function delete($id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}
