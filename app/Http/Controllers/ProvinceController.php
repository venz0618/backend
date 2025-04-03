<?php


namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Region;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    // Fetch all provinces
    public function index()
    {
        return response()->json(Province::with('region')->get());
    }

    // Store a new province
    public function store(Request $request)
    {
        $request->validate([
            'province_name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'province_status' => 'integer'
        ]);

        $province = Province::create($request->all());

        return response()->json($province, 201);
    }

    // Show a single province
    public function show($id)
    {
        $province = Province::with('region')->find($id);
        if (!$province) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        return response()->json($province);
    }

    // Update province
    public function update(Request $request, $id)
    {
        $province = Province::find($id);
        if (!$province) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        $request->validate([
            'province_name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'province_status' => 'integer'
        ]);

        $province->update($request->all());

        return response()->json($province);
    }

    // Delete province
    public function destroy($id)
    {
        $province = Province::find($id);
        if (!$province) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        $province->delete();

        return response()->json(['message' => 'Province deleted successfully']);
    }
    public function getProvincesByRegion($regionId)
    {
        return Province::where('region_id', $regionId)->get();
    }
}
