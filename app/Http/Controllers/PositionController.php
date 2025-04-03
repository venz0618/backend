<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
    public function index()
    {
        return response()->json(Position::all());
    }

    public function store(Request $request)
{
    $request->validate([
        'position_type' => 'required|string|unique:positions,position_type',
        'position_status' => 'required|integer',
        'level' => 'required|string', // Assuming 'level' is a string (e.g., 'National', 'Local')
        'max_votes' => 'required|integer|min:1' // Ensuring max_votes is a positive integer
    ]);

    $position = Position::create($request->only(['position_type', 'position_status', 'level', 'max_votes']));

    return response()->json($position, 201);
}


    public function show($id)
    {
        $position = Position::find($id);
        if (!$position) {
            return response()->json(['message' => 'Position not found'], 404);
        }
        return response()->json($position);
    }

    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);
        $request->validate([
            'position_type' => 'required|string|unique:positions,position_type,' . $id,
            'position_status' => 'required|integer'
        ]);

        $position->update($request->all());
        return response()->json($position);
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();
        return response()->json(['message' => 'Position deleted successfully']);
    }
}
