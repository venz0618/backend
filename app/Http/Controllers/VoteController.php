<?php   
namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Candidate;
use App\Models\Position;




class VoteController extends Controller
{
    /**
     * Display a paginated list of votes with filters.
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Store a newly created vote in the database.
     */
    public function store(Request $request)
    {
        $votes = $request->input('votes');
    
        // Ensure that $votes is an array
        if (!is_array($votes)) {
            return response()->json(['message' => 'Invalid votes format'], 400);
        }
    
        foreach ($votes as $vote) {
            if (!isset($vote['candidate_ids']) || !is_array($vote['candidate_ids'])) {
                return response()->json(['message' => 'Invalid candidate_ids format'], 400);
            }
    
            $voterId = $vote['voter_id'];
            $candidateIds = $vote['candidate_ids'];
    
            // Group candidates by position
            $positionVotes = [];
    
            foreach ($candidateIds as $candidateId) {
                $candidate = Candidate::find($candidateId);
                if (!$candidate) {
                    return response()->json(['message' => 'Candidate not found'], 404);
                }
    
                $positionId = $candidate->position_id;
                $position = Position::find($positionId);
    
                if (!$position) {
                    return response()->json(['message' => 'Position not found'], 404);
                }
    
                // Track votes per position
                if (!isset($positionVotes[$positionId])) {
                    $positionVotes[$positionId] = [];
                }
    
                $positionVotes[$positionId][] = $candidateId;
            }
    
            // Validate votes per position based on max_votes
            foreach ($positionVotes as $positionId => $candidates) {
                $position = Position::find($positionId);
                $maxVotes = $position->max_votes;
    
                // Count how many times the voter has already voted for this position
                $existingVoteCount = Vote::where('voter_id', $voterId)
                    ->whereHas('candidate', function ($query) use ($positionId) {
                        $query->where('position_id', $positionId);
                    })
                    ->count();
    
                $newVotesCount = count($candidates);
    
                if (($existingVoteCount + $newVotesCount) > $maxVotes) {
                    return response()->json([
                        'message' => "You can only vote for up to {$maxVotes} candidates in this position."
                    ], 403);
                }
    
                // Insert votes
                foreach ($candidates as $candidateId) {
                    Vote::create([
                        'voter_id' => $voterId,
                        'candidate_id' => $candidateId,
                        'num_votes' => 1
                    ]);
                }
            }
        }
    
        return response()->json(['message' => 'Votes submitted successfully'], 200);
    }
    


    /**
     * Display a specific vote by ID.
     */
    public function show($id)
    {
        
    }

    /**
     * Update an existing vote.
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove a vote from the database.
     */
    public function destroy($id)
    {
       
    }
    public function getVotedVoters()
    {
        $votedVoters = Vote::pluck('voter_id')->unique();
        return response()->json(['votedVoters' => $votedVoters]);
    }

}
