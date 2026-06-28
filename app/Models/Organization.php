<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'address',
        'description',
    ];

    // الجمعية تنتمي لمستخدم واحد أساسي
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
