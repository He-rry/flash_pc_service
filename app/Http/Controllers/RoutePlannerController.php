<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Route; // သိမ်းထားတဲ့ route တွေရှိရင်

class RoutePlannerController extends Controller
{
    public function index()
    {
        $shops = Shop::all(); // Database ထဲက ဆိုင်အားလုံးယူမယ်
        $routes = Route::all();

        return view('auth.index', compact('shops', 'routes'));
    }
}
