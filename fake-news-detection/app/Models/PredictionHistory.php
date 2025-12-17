<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredictionHistory extends Model
{
    protected $fillable = [
        'news_text',
        'prediction',
        'confidence_score',
        'explanation',
    ];

    protected $casts = [
        'explanation' => 'array',
    ];
}
