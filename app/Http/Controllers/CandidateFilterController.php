<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;

class CandidateFilterController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::query();

        if ($request->has('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        return response()->json($query->get());
    }
}
