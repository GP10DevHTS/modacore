<?php

namespace App\Http\Controllers;

use App\Models\InventoryVariant;
use Illuminate\Http\Request;

class BarCodeController extends Controller
{
    public function index($variation)
    {
        $variationValue = InventoryVariant::with('item')->findOrFail($variation);

        // dd($variationValue);

        return view('inventory.variation-bar-code', compact('variationValue'));
    }
}
