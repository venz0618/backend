<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Position;
use App\Models\Candidate;
use App\Models\Vote;

class SurveyController extends Controller
{
    // Get voters with filters
    public function getSurveyVoters(Request $request)
    {
        $query = Voter::query();

        // Apply filters
        if ($request->has('region_id')) {
            $query->whereHas('precinctNumber.clusteredPrecinct.barangay.city.province', function ($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }
        if ($request->has('province_id')) {
            $query->whereHas('precinctNumber.clusteredPrecinct.barangay.city', function ($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }
        if ($request->has('city_id')) {
            $query->whereHas('precinctNumber.clusteredPrecinct.barangay', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }
        if ($request->has('barangay_id')) {
            $query->whereHas('precinctNumber.clusteredPrecinct', function ($q) use ($request) {
                $q->where('barangay_id', $request->barangay_id);
            });
        }
        if ($request->has('clustered_precinct_id')) {
            $query->whereHas('precinctNumber', function ($q) use ($request) {
                $q->where('clustered_precinct_id', $request->clustered_precinct_id);
            });
        }
        if ($request->has('precinct_num_id')) {
            $query->where('precinct_num_id', $request->precinct_num_id);
        }
        if ($request->has('voter_name')) {
            $query->where('voter_name', 'like', '%' . $request->voter_name . '%');
        }

        // Paginate results
        $voters = $query->paginate($request->get('per_page', 20));

        // Get positions and candidates
        $positions = Position::where('position_status', 0)->with(['candidates' => function ($q) {
            $q->where('candidate_status', 0);
        }])->get();

        return response()->json([
            'voters' => $voters,
            'positions' => $positions
        ]);
    }

    // Submit a vote
    public function submitVote(Request $request)
    {
        $request->validate([
            'voter_id' => 'required|exists:voters,id',
            'candidate_id' => 'required|exists:candidates,id',
            'num_votes' => 'nullable|integer|min:1'
        ]);

        $vote = Vote::updateOrCreate(
            ['voter_id' => $request->voter_id, 'candidate_id' => $request->candidate_id],
            ['num_votes' => $request->get('num_votes', 1)]
        );

        return response()->json(['message' => 'Vote submitted successfully']);
    }
}
