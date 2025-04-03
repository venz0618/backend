<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\Voter;
use App\Models\PrecinctNumber;
use App\Models\ClusteredPrecinct;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getVoteReports(Request $request)
    {
        $level = $request->query('level', 'region');
        $position_id = $request->query('position_id');

        if (!$position_id) {
            return response()->json(['message' => 'Position ID is required'], 400);
        }

        $candidates = Candidate::where('position_id', $position_id)->get();
        $results = [];

        if ($level === 'region') {
            $regions = Region::all();
            foreach ($regions as $region) {
                $regionData = [
                    'id' => $region->id,
                    'name' => $region->region_name,
                    'candidates' => []
                ];

                foreach ($candidates as $candidate) {
                    $voteCount = Vote::where('candidate_id', $candidate->id)
                        ->whereHas('voter.precinctNumber.clusteredPrecinct.barangay.city.province', function ($query) use ($region) {
                            $query->where('region_id', $region->id);
                        })
                        ->sum('num_votes');

                    $regionData['candidates'][] = [
                        'id' => $candidate->id,
                        'name' => $candidate->candidate_name,
                        'votes' => $voteCount
                    ];
                }

                $results[] = $regionData;
            }
        }

        elseif ($level === 'province') {
            $query = Province::query();
            if ($request->query('region_id')) {
                $query->where('region_id', $request->query('region_id'));
            }
            $provinces = $query->get();

            foreach ($provinces as $province) {
                $provinceData = [
                    'id' => $province->id,
                    'name' => $province->province_name,
                    'candidates' => []
                ];

                foreach ($candidates as $candidate) {
                    $voteCount = Vote::where('candidate_id', $candidate->id)
                        ->whereHas('voter.precinctNumber.clusteredPrecinct.barangay.city', function ($query) use ($province) {
                            $query->where('province_id', $province->id);
                        })
                        ->sum('num_votes');

                    $provinceData['candidates'][] = [
                        'id' => $candidate->id,
                        'name' => $candidate->candidate_name,
                        'votes' => $voteCount
                    ];
                }

                $results[] = $provinceData;
            }
        }

        elseif ($level === 'city') {
            $query = City::query();
            if ($request->query('province_id')) {
                $query->where('province_id', $request->query('province_id'));
            }
            $cities = $query->get();

            foreach ($cities as $city) {
                $cityData = [
                    'id' => $city->id,
                    'name' => $city->city_name,
                    'candidates' => []
                ];

                foreach ($candidates as $candidate) {
                    $voteCount = Vote::where('candidate_id', $candidate->id)
                        ->whereHas('voter.precinctNumber.clusteredPrecinct.barangay', function ($query) use ($city) {
                            $query->where('city_id', $city->id);
                        })
                        ->sum('num_votes');

                    $cityData['candidates'][] = [
                        'id' => $candidate->id,
                        'name' => $candidate->candidate_name,
                        'votes' => $voteCount
                    ];
                }

                $results[] = $cityData;
            }
        }

        elseif ($level === 'barangay') {
            $query = Barangay::query();
            if ($request->query('city_id')) {
                $query->where('city_id', $request->query('city_id'));
            }
            $barangays = $query->get();

            foreach ($barangays as $barangay) {
                $barangayData = [
                    'id' => $barangay->id,
                    'name' => $barangay->barangay_name,
                    'candidates' => []
                ];

                foreach ($candidates as $candidate) {
                    $voteCount = Vote::where('candidate_id', $candidate->id)
                        ->whereHas('voter.precinctNumber.clusteredPrecinct', function ($query) use ($barangay) {
                            $query->where('barangay_id', $barangay->id);
                        })
                        ->sum('num_votes');

                    $barangayData['candidates'][] = [
                        'id' => $candidate->id,
                        'name' => $candidate->candidate_name,
                        'votes' => $voteCount
                    ];
                }

                $results[] = $barangayData;
            }
        }

        return response()->json([
            'level' => $level,
            'results' => $results
        ]);
    }
}
