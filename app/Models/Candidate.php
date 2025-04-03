<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_name',
        'position_id',
        'province_id',
        'candidate_status',
        'city_id', 'district_id', 'barangay_id'
    ];


    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
}
