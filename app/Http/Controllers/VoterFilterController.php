<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoterFilterController extends Controller
{
    public function filterVoters(Request $request)
    {
        // Log request data for debugging
        \Log::info('Filter request:', $request->all());
    
        $voters = DB::table('voters')
            ->join('precinct_numbers', 'voters.precinct_num_id', '=', 'precinct_numbers.id')
            ->join('clustered_precincts', 'precinct_numbers.clustered_precinct_id', '=', 'clustered_precincts.id')
            ->join('barangays', 'clustered_precincts.barangay_id', '=', 'barangays.id')
            ->join('cities', 'barangays.city_id', '=', 'cities.id')
            ->join('provinces', 'cities.province_id', '=', 'provinces.id')
            ->join('regions', 'provinces.region_id', '=', 'regions.id')
            ->when($request->region_id, function ($query, $region_id) {
                return $query->where('regions.id', (int)$region_id);
            })
            ->when($request->province_id, function ($query, $province_id) {
                return $query->where('provinces.id', (int)$province_id);
            })
            ->when($request->city_id, function ($query, $city_id) {
                return $query->where('cities.id', (int)$city_id);
            })
            ->when($request->barangay_id, function ($query, $barangay_id) {
                return $query->where('barangays.id', (int)$barangay_id);
            })
            ->when($request->clustered_precinct_id, function ($query, $clustered_precinct_id) {
                return $query->where('clustered_precincts.id', (int)$clustered_precinct_id);
            })
            ->when($request->precinct_num, function ($query, $precinct_num) {
                return $query->where('precinct_numbers.precinct_num', (int)$precinct_num);
            })
            ->select('voters.*')
            ->get();
    
        return response()->json($voters);
    }
    

    

}
