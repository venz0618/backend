<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        return City::with('province')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'city_name' => 'required',
            'province_id' => 'required|exists:provinces,id',
            'city_status' => 'integer',
        ]);

        return City::create($request->all());
    }
    public function show($id)
    {
        $city = City::with('province')->find($id);
        if (!$city) {
            return response()->json(['error' => 'City not found'], 404);
        }

        return response()->json($city);
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'city_name' => 'required',
            'province_id' => 'required|exists:provinces,id',
            'city_status' => 'integer',
        ]);

        $city->update($request->all());
        return $city;
    }

    public function destroy(City $city)
    {
        $city->delete();
        return response()->json(['message' => 'City deleted']);
    }
}
