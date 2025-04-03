<?php
namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    // Get all regions
    public function index()
    {
        return response()->json(Region::all());
    }

    // Store a new region
    public function store(Request $request)
    {
        $request->validate([
            'region_name' => 'required|string|unique:regions,region_name',
            'region_status' => 'required|integer'
        ]);

        $region = Region::create($request->all());
        return response()->json($region, 201);
    }

    // Show a specific region
    public function show($id)
    {
        return response()->json(Region::findOrFail($id));
    }

    // Update a region
    public function update(Request $request, $id)
    {
        $request->validate([
            'region_name' => 'required|string|unique:regions,region_name,' . $id,
            'region_status' => 'required|integer'
        ]);

        $region = Region::findOrFail($id);
        $region->update($request->all());
        return response()->json($region);
    }

    // Delete a region
    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        $region->delete();
        return response()->json(['message' => 'Region deleted']);
    }
}
