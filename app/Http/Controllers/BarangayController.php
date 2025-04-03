<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangay;

class BarangayController extends Controller
{
    public function index()
    {
        return Barangay::with('city')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'barangay_name' => 'required',
            'city_id' => 'required|exists:cities,id',
            'barangay_status' => 'integer',
        ]);

        return Barangay::create($request->all());
    }

    public function update(Request $request, Barangay $barangay)
    {
        $barangay->update($request->all());
        return $barangay;
    }

    public function destroy(Barangay $barangay)
    {
        $barangay->delete();
        return response()->json(['message' => 'Barangay deleted']);
    }
}
