<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;

class ShopController extends Controller
{
    // Page ကို ပြသခြင်း
    public function create()
    {
        return view('auth.routes.create');
    }

    // Database ထဲ သိမ်းဆည်းခြင်း
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'lat'  => 'required',
            'lng'  => 'required',
        ]);

        Shop::create([
            'name' => $request->name,
            'lat'  => $request->lat,
            'lng'  => $request->lng,
        ]);

        // သိမ်းပြီးတာနဲ့ Route Planner ဆီ တန်းပို့မယ်
        return redirect()->route('routes.index')->with('success', 'Shop added successfully!');
    }
}
