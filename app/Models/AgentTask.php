<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'donation_id',
        'title',
        'description',
        'qr_code',
        'status'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}
