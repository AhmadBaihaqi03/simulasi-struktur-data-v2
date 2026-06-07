<?php

namespace App\Models;

use App\Models\GroupEvaluation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GroupAnswer extends Model
{
    use HasFactory;

    // Menghubungkan model ini ke tabel 'group_answers'
    protected $table = 'group_answers';

    protected $fillable = [
        'pbl_group_session_id', 
        'group_name', 
        'student_data', 
        'class_name',
        'is_submitted',
        'f3_answers',
        'f4_link', 
        'f4_answers', 
        'f5_answers',
    ];

    protected $casts = [
        'student_data' => 'array',
        'f3_answers' => 'array',
        'f5_answers' => 'array',
        'is_submitted' => 'boolean',
    ];

    /**
     * Relasi ke Sesi PBL
     */
    public function pblGroupSession(): BelongsTo
    {
        return $this->belongsTo(PblGroupSession::class, 'pbl_group_session_id');
    }

    /*
     * Relasi ke hasil evaluasi
     */
    public function groupEvaluation(): HasOne
    {
        return $this->hasOne(GroupEvaluation::class);
    }
}