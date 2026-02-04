<?php
namespace App\Http\Controllers;
use App\Services\ServiceService;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    protected $service;

    public function __construct(ServiceService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $services = $this->service->getServiceList();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        Gate::authorize('manage-services');
        $data = $this->service->getInitialData();
        return view('services.create', ['types' => $data['types']]);
    }

    public function store(StoreServiceRequest $request)
    {
        Gate::authorize('manage-services');
        $this->service->createReport($request->validated());
        return redirect()->route('admin.services.index')->with('success', 'Service created successfully!');
    }

    public function edit($id)
    {
        Gate::authorize('manage-services');
        $service = $this->service->find($id);
        $data = $this->service->getInitialData();

        return view('services.edit', [
            'service' => $service,
            'statuses' => $data['statuses'],
            'types' => $data['types']
        ]);
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        Gate::authorize('manage-services');
        $this->service->updateRecord($id, $request->validated());
        return redirect()->route('admin.services.index')->with('success', 'Updated!');
    }

    public function destroy($id)
    {
        Gate::authorize('manage-services');
        $this->service->deleteRecord($id);
        return redirect()->route('admin.services.index')->with('success', 'Deleted!');
    }

    public function storeCustomerReport(StoreServiceRequest $request)
    {
        $this->service->createReport($request->validated());
        return redirect()->back()->with('success', 'လက်ခံရရှိပါပြီ။');
    }

    public function track(Request $request)
    {
        if (! $request->filled('phone')) {
            return view('customers.track')->with('service');
        }

        $service = $this->service->getTrackInfo($request->phone);

        return view('customers.track', compact('service'));
    }
}
