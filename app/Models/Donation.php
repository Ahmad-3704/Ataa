<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'amount',
        'status',
    ];

    // علاقة التبرع بالمتبرع (المستخدم)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة التبرع بالمشروع
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
