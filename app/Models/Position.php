<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['position_type', 'position_status','level', 'max_votes'];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
