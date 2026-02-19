<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Services\StatusService;
use Illuminate\Support\Facades\Gate;

class StatusController extends Controller
{
    protected $service;

    public function __construct(StatusService $service)
    {
        $this->service = $service;
        $this->middleware('permission:manage-services')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $statuses = $this->service->list();
        return view('status.index', compact('statuses'));
    }

    public function create()
    {
        Gate::authorize('manage-services');
        return view('status.create');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateStatus($request);

        $this->service->create($validatedData);

        return redirect()->route('admin.statuses.index')->with('success', 'Status created!');
    }

    public function edit($id)
    {
        Gate::authorize('manage-services');
        $status = $this->service->find($id);
        return view('status.edit', compact('status'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('manage-services');
        $validated = $this->validateStatus($request, true);

        $this->service->update($id, $validated);

        return redirect()->route('admin.statuses.index')->with('success', 'Status updated!');
    }

    private function validateStatus(Request $request, $isUpdate = false)
    {
        $rules = ['status_name' => 'required|max:50'];

        if (! $isUpdate) {
            $rules['status_name'] = 'required|unique:statuses|max:50';
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
            return redirect()->back()->with('error', 'ဖျက်လို့မရပါဘူး။ ဒီ Status ကို အသုံးပြုထားတဲ့ Service records တွေ ရှိနေပါသေးတယ်။');
        }
    }
}
