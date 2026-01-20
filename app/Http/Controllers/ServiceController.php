<?php

namespace App\Http\Controllers;

use App\Services\RepairBusinessService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $repairService;

    public function __construct(RepairBusinessService $repairService)
    {
        $this->repairService = $repairService;
    }

    public function index()
    {
        $services = $this->repairService->getServiceList();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $data = $this->repairService->getInitialData();
        return view('services.create', ['types' => $data['types']]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|min:7|max:20',
            'customer_address' => 'required|string',
            'service_type_id' => 'required|exists:service_types,id',
        ]);

        $this->repairService->createReport($validated);
        return redirect()->route('services.index')->with('success', 'Service created successfully!');
    }

    public function edit($id)
    {
        $service = \App\Models\Service::findOrFail($id);
        $data = $this->repairService->getInitialData();
        return view('services.edit', [
            'service' => $service,
            'statuses' => $data['statuses'],
            'types' => $data['types']
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'status_id' => 'required|exists:statuses,id',
            'service_type_id' => 'required|exists:service_types,id',
        ]);

        $this->repairService->updateRecord($id, $validated);
        return redirect()->route('services.index')->with('success', 'Updated!');
    }

    public function destroy($id)
    {
        $this->repairService->deleteRecord($id);
        return redirect()->route('services.index')->with('success', 'Deleted!');
    }

    public function storeCustomerReport(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required',
            'customer_address' => 'required',
            'service_type_id' => 'required',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
        ]);

        $this->repairService->createReport($validated);
        return redirect()->back()->with('success', 'လက်ခံရရှိပါပြီ။');
    }

    public function track(Request $request)
    {
        $service = $request->filled('phone') ? $this->repairService->getTrackInfo($request->phone) : null;
        return view('customers.track', compact('service'));
    }
};
