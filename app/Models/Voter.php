<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    use HasFactory;

    protected $fillable = ['voter_name', 'precinct_num_id', 'voter_status'];

    public function precinctNumber()
    {
        return $this->belongsTo(PrecinctNumber::class, 'precinct_num_id');
    }

    // public function votes()
    // {
    //     return $this->hasMany(Vote::class);
    // }
}
