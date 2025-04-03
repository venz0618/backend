<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\ClusteredPrecinctController;
use App\Http\Controllers\PrecinctNumberController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VoterFilterController;
use App\Http\Controllers\CandidateFilterController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VoteResultController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

   
});

Route::apiResource('regions', RegionController::class);
Route::apiResource('provinces', ProvinceController::class);
Route::apiResource('cities', CityController::class);
Route::apiResource('barangays', BarangayController::class);
Route::apiResource('clustered-precincts', ClusteredPrecinctController::class);
Route::apiResource('precincts', PrecinctNumberController::class);
Route::get('/precinct-numbers', [PrecinctNumberController::class, 'getPrecincts']);
// Route::get('/clustered-precincts/{barangayId}', [ClusteredPrecinctController::class, 'show']);
Route::get('/precincts/{clustered_precinct_id}', [PrecinctNumberController::class, 'show']);
Route::apiResource('voters', VoterController::class);
Route::apiResource('positions', PositionController::class);
Route::apiResource('candidates', CandidateController::class);
Route::get('/dashboard-stats', [DashboardController::class, 'getStats']);


Route::get('/voters/filter', [VoterFilterController::class, 'filterVoters']);
Route::get('/voters/{voter}', [VoterController::class, 'show']);



Route::get('/provinces/{regionId}', [ProvinceController::class, 'getProvincesByRegion']);



Route::get('/candidates-filter', [CandidateFilterController::class, 'index']);
Route::post('/submit-votes', [VoteController::class, 'store']);
Route::get('/voted-voters', [VoteController::class, 'getVotedVoters']);
Route::post('/voters/import', [VoterController::class, 'importVoters']);
Route::post('/voters/bulk', [VoterController::class, 'storeBulk']);






Route::get('/region', [VoteResultController::class, 'getRegions']);
Route::get('/province', [VoteResultController::class, 'getProvinces']);
Route::get('/city', [VoteResultController::class, 'getCities']);
Route::get('/barangay', [VoteResultController::class, 'getBarangays']);
Route::get('/clustered-precinct', [VoteResultController::class, 'getClusteredPrecincts']);
Route::get('/precinct-number', [VoteResultController::class, 'getPrecinctNumbers']);
Route::get('/results', [VoteResultController::class, 'getResults']);
Route::get('/filterVotes', [VoteResultController::class, 'filterVotes'])->name('votes.filter');

// Route::get('filter-votes/{precinct_number}', [VoteResultController::class, 'filterVoter']);
Route::get('filter-votes', [VoteResultController::class, 'filterVoter']);
