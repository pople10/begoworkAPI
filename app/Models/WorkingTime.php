<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingTime extends Model
{
    use HasFactory;
    
    protected $casts = [
        'startTime' => 'datetime:H:i:s',
        'endTime' => 'datetime:H:i:s',
    ];
}
