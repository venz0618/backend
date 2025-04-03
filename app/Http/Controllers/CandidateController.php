<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;

class CandidateController extends Controller
{
    public function index()
    {
        return response()->json(Candidate::with(['position', 'province'])->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'candidate_name' => 'required|string|max:255',
            'position_id' => 'required|exists:positions,id',
            'province_id' => 'required|exists:provinces,id',
            'candidate_status' => 'required|integer'
        ]);

        Candidate::create($request->all());

        return response()->json(['message' => 'Candidate added successfully'], 201);
    }

    public function update(Request $request, Candidate $candidate)
    {
        $request->validate([
            'candidate_name' => 'required|string|max:255',
            'position_id' => 'required|exists:positions,id',
            'province_id' => 'required|exists:provinces,id',
            'candidate_status' => 'required|integer'
        ]);

        $candidate->update($request->all());

        return response()->json(['message' => 'Candidate updated successfully'], 200);
    }

    public function destroy(Candidate $candidate)
    {
        $candidate->delete();
        return response()->json(['message' => 'Candidate deleted successfully'], 200);
    }
}
