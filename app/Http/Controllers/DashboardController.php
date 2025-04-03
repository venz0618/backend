<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate; // Example Model
use App\Models\Voter;
use App\Models\PrecinctNumber;
use App\Models\Vote;

class DashboardController extends Controller
{
    public function getStats()
    {
        // Get the unique voter IDs from the Vote table
    $votedVoters = Vote::pluck('voter_id')->unique();

    // Count the unique voters who have voted
    $votedVotersCount = $votedVoters->count();

    // Return the stats
    return response()->json([
        'total_candidates' => Candidate::count(),
        'total_voters' => Voter::count(),
        'total_precincts' => PrecinctNumber::count(),
        'total_voted' => $votedVotersCount, // Add the count of unique voters who voted
    ]);
    }
}
