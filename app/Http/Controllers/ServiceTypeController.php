<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Services\ServiceTypeService;
use Illuminate\Support\Facades\Gate;

class ServiceTypeController extends Controller
{
    protected $service;

    public function __construct(ServiceTypeService $service)
    {
        $this->service = $service;
        $this->middleware('permission:manage-services')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $types = $this->service->list();
        return view('service_types.index', compact('types'));
    }

    public function create()
    {
        Gate::authorize('manage-services');
        return view('admin.service_types.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateServiceType($request);

        $this->service->create($validated);

        return redirect()->route('admin.service-types.index')->with('success', 'Service Type Created!');
    }

    public function edit($id)
    {
        Gate::authorize('manage-services');
        $type = $this->service->find($id);
        return view('service_types.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('manage-services');
        $validated = $this->validateServiceType($request, true);

        $this->service->update($id, $validated);

        return redirect()->route('admin.service-types.index')->with('success', 'Service Type Updated!');
    }

    private function validateServiceType(Request $request, $isUpdate = false)
    {
        $rules = ['service_name' => 'required'];

        if (! $isUpdate) {
            $rules['service_name'] = 'required|unique:service_types,service_name';
        }

        return $request->validate($rules);
    }

    public function destroy($id)
    {
        Gate::authorize('manage-services');
        try {
            $this->service->delete($id);

            return redirect()->back()->with('success', 'Deleted successfully!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'ဖျက်လို့မရပါဘူး။ ဒီ Service Type ကို အသုံးပြုထားတဲ့ Service records တွေ ရှိနေပါသေးတယ်။');
        }
    }
}
