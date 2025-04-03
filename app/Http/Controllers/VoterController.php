<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;
use Illuminate\Support\Facades\DB; // ✅ Import DB

class VoterController extends Controller
{
    public function index()
    {
        return Voter::with('precinctNumber')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'voter_name' => 'required|string',
            'precinct_num_id' => 'required|exists:precinct_numbers,id',
        ]);

        $voter = Voter::create($request->all());
        return response()->json($voter, 201);
    }

    public function show(Request $request, $id)
    {
        $id = (int) $id; // Ensure ID is an integer
    
        $voters = Voter::where('precinct_num_id', $request->precinct_num)->get();
        return response()->json($voters);
    }
    



    public function update(Request $request, $id)
    {
        $request->validate([
            'voter_name' => 'required|string',
            'precinct_num_id' => 'required|exists:precinct_numbers,id',
        ]);

        // Fetch voter
        $voter = Voter::find($id);

        if (!$voter) {
            return response()->json(['message' => 'Voter not found'], 404);
        }

        // Update voter
        $voter->update($request->all());

        return response()->json($voter);
    }

    public function destroy($id)
    {
        Voter::destroy($id);
        return response()->json(['message' => 'Voter deleted successfully']);
    }

    public function importVoters(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $inserted = 0;
        $duplicates = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header row

            $voter_name = trim($row[0]); // Assuming first column is voter name

            if (empty($voter_name)) continue;

            // Check for duplicate
            if (Voter::where('voter_name', $voter_name)->exists()) {
                $duplicates[] = $voter_name;
                continue;
            }

            // Insert voter without precinct_num_id (User will manually set it later)
            Voter::create([
                'voter_name' => $voter_name,
                'precinct_num_id' => null, // To be assigned later
                'voter_status' => 0, // Default status
            ]);

            $inserted++;
        }

        return response()->json([
            'message' => 'Voters imported successfully!',
            'inserted' => $inserted,
            'duplicates' => $duplicates,
        ], 200);
    }
    
    public function storeBulk(Request $request)
    {
        // ✅ Fix Validation Rules
        $request->validate([
            'voters' => 'required|array',
            'voters.*.voter_name' => 'required|string|max:255',
            'voters.*.precinct_num_id' => 'required|exists:precinct_numbers,id',
            'voters.*.voter_status' => 'required|integer',
        ]);
    
        // ✅ Fix Query to Check Existing Voters in Multiple Precincts
        $existingVoters = Voter::whereIn('precinct_num_id', collect($request->voters)->pluck('precinct_num_id'))
                               ->get()
                               ->groupBy('precinct_num_id')
                               ->map(fn($group) => $group->pluck('voter_name')->toArray());
    
        $bulkVoters = [];
    
        foreach ($request->voters as $voter) {
            if (!isset($existingVoters[$voter['precinct_num_id']]) ||
                !in_array($voter['voter_name'], $existingVoters[$voter['precinct_num_id']])) 
            {
                // ✅ Add voter if not already in the same precinct
                $bulkVoters[] = [
                    'voter_name' => $voter['voter_name'],
                    'precinct_num_id' => $voter['precinct_num_id'], // ✅ Use correct data
                    'voter_status' => $voter['voter_status'], // ✅ Use correct data
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
    
        if (!empty($bulkVoters)) {
            Voter::insert($bulkVoters);
        }
    
        return response()->json([
            'message' => count($bulkVoters) > 0 ? 'Voters added successfully' : 'No new voters were added (duplicates found).',
            'added_count' => count($bulkVoters),
        ], 201);
    }
    


}
