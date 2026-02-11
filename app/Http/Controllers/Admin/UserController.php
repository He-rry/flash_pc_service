<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Http\Requests\StoreUserRequest; 
use App\Http\Requests\UpdateUserRequest; 
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    protected $userRepo;
    protected $userService;

    public function __construct(UserRepository $userRepo, UserService $userService)
    {
        $this->userRepo = $userRepo;
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userRepo->getPaginatedUsers();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->userRepo->getAllRoles();
        $permissions = $this->userRepo->getAllPermissions();

        // Logic Service
        $matrix = $this->userService->getPermissionMatrix();
        $rolePerms = $this->userService->getRolePermissionsMap();

        return view('admin.users.create', compact('roles', 'matrix', 'permissions', 'rolePerms'));
    }

    public function store(StoreUserRequest $request)
    {
        // Validation 
        $data = $request->validated();
        // Checkbox empty array handle
        $data['custom_permissions'] = $request->input('custom_permissions', []);

        $this->userService->createAdminUser($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        Gate::authorize('manage', $user);

        $roles = $this->userRepo->getAllRoles();
        $permissions = $this->userRepo->getAllPermissions();

        $userRoles = $user->roles->pluck('name')->toArray();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        $matrix = $this->userService->getPermissionMatrix();
        $rolePerms = $this->userService->getRolePermissionsMap();

        return view('admin.users.edit', compact(
            'user',
            'roles',
            'permissions',
            'userRoles',
            'userPermissions',
            'matrix',
            'rolePerms'
        ));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $data['custom_permissions'] = $request->input('custom_permissions', []);

        $this->userService->updateAdminUser($user, $data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        // Policy Check User Object
        $user = $this->userRepo->find($id);
        Gate::authorize('delete', $user);

        try {
            $this->userService->deleteUser($id);
            return redirect()->route('admin.users.index')->with('success', 'User has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // AJAX Call
    public function getRolePermissions($roleName)
    {
        $permissions = $this->userRepo->getPermissionsByRoleName($roleName);
        return response()->json($permissions);
    }

    public function restore($id)
    {
        try {
            $this->userService->restoreUser($id);
            return redirect()->back()->with('success', 'User has been restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->userService->forceDeleteUser($id);
            return redirect()->back()->with('success', 'User has been permanently deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
