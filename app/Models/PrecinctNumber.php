<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrecinctNumber extends Model
{
    use HasFactory;

    protected $fillable = ['precinct_num', 'clustered_precinct_id'];

    public function clusteredPrecinct()
    {
        return $this->belongsTo(ClusteredPrecinct::class, 'clustered_precinct_id');
    }

    public function voters()
    {
        return $this->hasMany(Voter::class, 'precinct_num_id');
    }
}
