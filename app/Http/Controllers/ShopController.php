<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;

class ShopController extends Controller
{
    public function create()
    {
        return view('auth.maps.create'); // resources/views/admin/maps/create.blade.php
    }

    public function store(Request $request)
    {
        // ... validation logic ...
        $request->validate([
            'name' => 'required',
            'lat'  => 'required',
            'lng'  => 'required',
        ]);
        Shop::create($request->all());

        // သိမ်းပြီးရင် admin.maps.index ကို ပြန်သွားခိုင်းပါ
        return redirect()->route('admin.maps.index')->with('success', 'Shop added!');
    }
}
