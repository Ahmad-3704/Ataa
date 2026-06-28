<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'is_anonymous'];

    protected function casts(): array
    {
        return ['is_anonymous' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
