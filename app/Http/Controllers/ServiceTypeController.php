<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{

    public function index()
    {
        $types = \App\Models\ServiceType::all();
        return view('service_types.index', compact('types'));
    }

    public function create()
    {
        return view('service_types.create');
    }

    public function store(Request $request)
    {
        $request->validate(['service_name' => 'required|unique:service_types,service_name']);
        \App\Models\ServiceType::create($request->only('service_name'));
        return redirect()->route('service-types.index')->with('success', 'Service Type Created!');
    }

    public function edit($id)
    {
        $type = \App\Models\ServiceType::findOrFail($id);
        return view('service_types.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['service_name' => 'required']);
        $type = \App\Models\ServiceType::findOrFail($id);
        $type->update($request->only('service_name'));
        return redirect()->route('service-types.index')->with('success', 'Service Type Updated!');
    }

    public function destroy($id)
    {

        try {
            $item = \App\Models\ServiceType::findOrFail($id);
            $item->delete();

            return redirect()->back()->with('success', 'Deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'ဖျက်လို့မရပါဘူး။ ဒီ Service Type ကို အသုံးပြုထားတဲ့ Service records တွေ ရှိနေပါသေးတယ်။');
        }
    }
}
