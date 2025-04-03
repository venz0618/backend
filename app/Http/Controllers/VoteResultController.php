<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Region,
    Province,
    City,
    Barangay,
    ClusteredPrecinct,
    PrecinctNumber,
    Vote
};
use App\Events\VoteUpdated;

class VoteResultController extends Controller
{
    public function getRegions()
    {
        return Region::where('region_status', 1)->get(['id', 'region_name as name']);
    }

    public function getProvinces(Request $request)
    {
        return Province::where('region_id', $request->parent)
            ->where('province_status', 1)
            ->get(['id', 'province_name as name']);
    }

    public function getCities(Request $request)
    {
        return City::where('province_id', $request->parent)
            ->where('city_status', 1)
            ->get(['id', 'city_name as name']);
    }

    public function getBarangays(Request $request)
    {
        return Barangay::where('city_id', $request->parent)
            ->where('barangay_status', 1)
            ->get(['id', 'barangay_name as name']);
    }

    public function getClusteredPrecincts(Request $request)
    {
        return ClusteredPrecinct::where('barangay_id', $request->parent)
            ->where('clustered_status', 1)
            ->get(['id', 'clustered_precinct_num as name']);
    }

    public function getPrecinctNumbers(Request $request)
    {
        return PrecinctNumber::where('clustered_precinct_id', $request->parent)
            ->get(['id', 'precinct_num as name']);
    }

    public function getResults(Request $request)
    {
        try {
            $query = Vote::with([
                'candidate',
                'voter.precinctNumber.clusteredPrecinct.barangay.city.province.region'
            ]);
    
            // Apply filters safely
            if ($request->filled('region')) {
                $query->whereHas('voter.precinctNumber.clusteredPrecinct.barangay.city.province.region', function ($q) use ($request) {
                    $q->where('id', $request->region); // Ensure the correct region_id field
                });
            }
            // Add other filters similarly...
    
            $votes = $query->get();
    
            // Group by candidate and sum votes
            $candidateTotals = $votes->groupBy('candidate_id')->map(function ($group) {
                return [
                    'candidate_name' => $group->first()->candidate->candidate_name,
                    'total_votes' => $group->sum('num_votes')
                ];
            })->values(); // Convert collection to array
    
            return response()->json([
                'total_votes_per_candidate' => $candidateTotals
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateResults(Request $request)
    {
        $vote = Vote::create($request->all());
        
        $updatedResults = $this->getResults(new Request([
            'precinctNumber' => $vote->voter->precinct_num_id
        ]));
        
        event(new VoteUpdated($updatedResults));
        
        return response()->json(['success' => true]);
    }

    public function filterVotes(Request $request)
{
    $cityId = $request->city_id;
    $barangayId = $request->barangay_id;
    $precinctNumId = $request->precinct_num_id;
    $clusteredPrecinctId = $request->clustered_precinct_id;
    $positionId = $request->position_id; // Get the position_id from the request

    // If no filters are selected, return all votes for all positions
    if (!$cityId && !$barangayId && !$clusteredPrecinctId &&!$precinctNumId && !$positionId) {
        $voteSummary = Vote::with('candidate')
            ->selectRaw('candidate_id, SUM(num_votes) as total_votes')
            ->groupBy('candidate_id')
            ->get()
            ->map(function ($vote) {
                return [
                    'candidate_name' => $vote->candidate->candidate_name ?? 'Unknown',
                    'total_votes' => $vote->total_votes
                ];
            });

        return response()->json(['votes' => $voteSummary]);
    }

    // Query votes based on filters (city, barangay, precinct, and position)
    $query = Vote::query()
        ->join('candidates', 'votes.candidate_id', '=', 'candidates.id')
        ->join('voters', 'votes.voter_id', '=', 'voters.id') // Join with voters table
        ->join('precinct_numbers', 'voters.precinct_num_id', '=', 'precinct_numbers.id') // Join with precinct_numbers table
        ->join('clustered_precincts', 'precinct_numbers.clustered_precinct_id', '=', 'clustered_precincts.id') // Join with clustered_precincts table
        ->join('barangays', 'clustered_precincts.barangay_id', '=', 'barangays.id') // Join with barangays table
        ->join('cities', 'barangays.city_id', '=', 'cities.id') // Join with cities table
        ->join('provinces', 'cities.province_id', '=', 'provinces.id') // Join with provinces table
        ->join('positions', 'candidates.position_id', '=', 'positions.id') // Join with positions table
        ->select(
            'candidates.id',
            'candidates.candidate_name',
            'cities.city_name',
            'provinces.province_name',
            'positions.position_type', // Include the position type in the selection
            \DB::raw('SUM(votes.num_votes) as total_votes') // Sum of total votes
        )
        ->groupBy('candidates.id','candidates.candidate_name', 'cities.city_name', 'provinces.province_name', 'positions.position_type')
        ->orderByDesc('total_votes');

    // Apply filters
    if ($cityId) {
        $query->where('cities.id', $cityId);
    }

    if ($barangayId) {
        $query->where('barangays.id', $barangayId);
    }

    if ($clusteredPrecinctId) {
        // Fixed: Use precinct_numbers.id instead of votes.precinct_num_id
        $query->where('clustered_precincts.id', $clusteredPrecinctId);
    }

    if ($precinctNumId) {
        $query->where('precinct_numbers.id', $precinctNumId); // Apply precinct number filter
    }
    

    // Apply position filter
    if ($positionId) {
        $query->where('candidates.position_id', $positionId); // Filter by position
    }

    $votes = $query->get();

    // Format and group the response based on city or barangay
    $formattedVotes = $votes->groupBy('position_type')->map(function ($positionVotes) {
        return $positionVotes->map(function ($vote) {
            return [
                'id' => $vote->id,
                'candidate_name' => $vote->candidate_name,
                'total_votes' => $vote->total_votes,
                'position' => $vote->position_type
            ];
        })->values();
    });

    return response()->json(['votes' => $formattedVotes]);
}

// app/Http/Controllers/VoteController.php
public function filterVoter(Request $request)
{
    // Get the precinct_num_id from the query string
    $precinctNumId = $request->query('precinct_num_id');  

    // Use Laravel's Query Builder to run the query
    $votes = DB::table('votes as v')
        ->join('candidates as c', 'v.candidate_id', '=', 'c.id')
        ->join('voters as vo', 'v.voter_id', '=', 'vo.id')
        ->join('precinct_numbers as p', 'vo.precinct_num_id', '=', 'p.id')
        ->select('vo.voter_name', 'c.candidate_name', 'p.precinct_num', DB::raw('SUM(v.num_votes) as total_votes'))
        ->where('p.id', $precinctNumId)  // Filter by precinct_num_id
        ->groupBy('vo.voter_name', 'c.candidate_name', 'p.precinct_num')
        ->orderByDesc('total_votes')
        ->get();  // Get the results as a collection

    // Return the results as a JSON response
    return response()->json(['votes' => $votes]);
}









    


}