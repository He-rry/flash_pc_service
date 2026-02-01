<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // Api/ShopController.php
    public function index(Request $request)
    {
        $query = Shop::with('admin')->applyFilters($request->all())->latest();
        $allFiltered = (clone $query)->get(); // Map အတွက် Marker အားလုံးယူမယ်
        $paginated = $query->paginate(10); // Table အတွက် pagination နဲ့ ယူမယ်

        return response()->json(array_merge(
            $paginated->toArray(),
            ['all_filtered' => $allFiltered]
        ));
    }
}
