<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupEvaluation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'group_answer_id',
        'feedback_comment'
    ];

    public function group()
    {
        return $this->belongsTo(GroupAnswer::class);
    }
}