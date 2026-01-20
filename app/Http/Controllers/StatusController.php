<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = \App\Models\Status::all();
        return view('status.index', compact('statuses'));
    }

    public function create()
    {
        return view('status.create');
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'status_name' => 'required|unique:statuses|max:50'
        ]);

        \App\Models\Status::create($validatedData);

        return redirect()->route('statuses.index')->with('success', 'Status created!');
    }

    public function edit($id)
    {
        $status = \App\Models\Status::findOrFail($id);
        return view('status.edit', compact('status'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['status_name' => 'required|max:50']);
        $status = \App\Models\Status::findOrFail($id);
        $status->update($request->all());
        return redirect()->route('admin.statuses.index')->with('success', 'Status updated!');
    }

    public function destroy($id)
    {
        try {
            $item = \App\Models\Status::findOrFail($id);
            $item->delete();

            return redirect()->back()->with('success', 'Deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'ဖျက်လို့မရပါဘူး။ ဒီ Status ကို အသုံးပြုထားတဲ့ Service records တွေ ရှိနေပါသေးတယ်။');
        }
    }
}
