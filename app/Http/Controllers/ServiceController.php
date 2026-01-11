<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceType;
use App\Models\Status;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        // Relationship တွေပါ တစ်ခါတည်း ပါအောင် with() သုံး
        $services = \App\Models\Service::with(['status', 'serviceType'])->paginate(10);

        return view('services.index', compact('services'));
    }
    public function create()
    {
        // Database ထဲက Service Type တွေကို ဆွဲထုတ်ပြီး View ဆီ ပို့
        $types = \App\Models\ServiceType::all();
        return view('services.create', compact('types'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|min:7|max:20',
            'customer_address' => 'required|string',
            'customer_email'   => 'nullable|email',
            'pc_model'         => 'nullable|string|max:100',
            'service_type_id'  => 'required|exists:service_types,id',
        ]);
        $status = Status::where('name', 'New')->first() ?? Status::first();

        if (!$status) {
            return back()->withErrors(['error' => 'Please seed the statuses table first!']);
        }

        // ၃။ Mass Assignment Protection (Validated data ကိုပဲသုံးမယ်)
        $validated['status_id'] = $status->id;
        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Service created successfully!');
    }
    public function edit($id)
    {
        $service = \App\Models\Service::findOrFail($id);
        $statuses = \App\Models\Status::all();
        $types = \App\Models\ServiceType::all();

        return view('services.edit', compact('service', 'statuses', 'types'));
    }

    // ပြင်ဆင်လိုက်တဲ့ data တွေကို database ထဲ သိမ်း
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'customer_name'   => 'required|string|max:255',
            'customer_phone'  => 'required|string',
            'status_id'       => 'required|exists:statuses,id',
            'service_type_id' => 'required|exists:service_types,id',
        ]);

        // အကုန်လုံးကို update မလုပ်ဘဲ စစ်ထားတဲ့ data ကိုပဲ updateလုပ်
        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Service updated successfully!');
    }

    public function destroy($id)
    {
        //ဖျက်မယ့် record ကို ရှာ
        $service = \App\Models\Service::findOrFail($id);
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service task deleted successfully!');
    }

    //


    public function storeCustomerReport(Request $request)
    {
        // Customer ဘက်ကလာတဲ့ data testing
        $validated = $request->validate([
            'customer_name'     => 'required|string|max:100',
            'customer_phone'    => 'required|string|min:7',
            'customer_address'  => 'required|string',
            'service_type_id'   => 'required|exists:service_types,id',
            'issue_description' => 'required|string|max:1000',
            'pc_model'          => 'nullable|string|max:100',
            'lat'               => 'required|numeric',
            'long'              => 'required|numeric',
        ]);

        $status = Status::where('status_name', 'New')->first() ?? Status::first();

        $validated['status_id'] = $status->id;
        Service::create($validated);

        return redirect()->back()->with('success', 'သင့်ရဲ့ Report ကို လက်ခံရရှိပါပြီ။');
    }
    public function track(Request $request)
    {
        $service = null;

        if ($request->filled('phone')) {
            //SQL Injection ကာကွယ်ရန် Query Builder
            $service = Service::where('customer_phone', $request->phone)
                ->with('status')
                ->latest()
                ->first();
        }

        return view('customers.track', compact('service'));
    }
}
