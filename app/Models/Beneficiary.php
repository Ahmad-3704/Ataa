<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'national_id', 'address', 'family_members_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
