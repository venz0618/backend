<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrecinctNumber;

class PrecinctNumberController extends Controller
{
    public function index()
    {
        return PrecinctNumber::with('clusteredPrecinct')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'precinct_num' => 'required|string|unique:precinct_numbers,precinct_num',
            'clustered_precinct_id' => 'required|exists:clustered_precincts,id',
        ]);

        return PrecinctNumber::create($request->all());
    }

    public function show($clustered_precinct_id)
    {
        $precincts = PrecinctNumber::where('clustered_precinct_id', $clustered_precinct_id)->get();
    
        if ($precincts->isEmpty()) {
            return response()->json(['message' => 'No precincts found'], 404);
        }
    
        return response()->json($precincts, 200);
    }
    

    public function update(Request $request, $id)
    {
        $precinct = PrecinctNumber::findOrFail($id);
        $request->validate([
            'precinct_num' => 'required|string|unique:precinct_numbers,precinct_num,' . $id,
            'clustered_precinct_id' => 'required|exists:clustered_precincts,id',
        ]);

        $precinct->update($request->all());
        return $precinct;
    }

    public function destroy($id)
    {
        $precinct = PrecinctNumber::findOrFail($id);
        $precinct->delete();
        return response()->json(['message' => 'Precinct deleted successfully']);
    }
    public function getPrecincts(Request $request) {
        $barangayId = $request->query('barangay_id');
    
        // Fetch precinct numbers by joining clustered precincts
        $precincts = PrecinctNumber::whereHas('clusteredPrecinct', function ($query) use ($barangayId) {
                $query->where('barangay_id', $barangayId);
            })
            ->select('id', 'precinct_num', 'clustered_precinct_id')
            ->get();
    
        return response()->json($precincts);
    }
    
    
    
}
