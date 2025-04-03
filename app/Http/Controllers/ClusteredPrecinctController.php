<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClusteredPrecinct;

class ClusteredPrecinctController extends Controller
{
    public function index()
    {
        return ClusteredPrecinct::with('barangay')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'clustered_precinct_num' => 'required|integer',
            'barangay_id' => 'required|exists:barangays,id',
            'clustered_status' => 'integer',
        ]);

        return ClusteredPrecinct::create($request->all());
    }
    public function show($barangayId)
    {
        $clusteredPrecincts = ClusteredPrecinct::where('barangay_id', $barangayId)->get();

        if ($clusteredPrecincts->isEmpty()) {
            return response()->json(['message' => 'No clustered precincts found'], 404);
        }

        return response()->json($clusteredPrecincts, 200);
    }


    public function update(Request $request, ClusteredPrecinct $clusteredPrecinct)
    {
        $clusteredPrecinct->update($request->all());
        return $clusteredPrecinct;
    }

    public function destroy(ClusteredPrecinct $clusteredPrecinct)
    {
        $clusteredPrecinct->delete();
        return response()->json(['message' => 'Clustered Precinct deleted']);
    }
}
