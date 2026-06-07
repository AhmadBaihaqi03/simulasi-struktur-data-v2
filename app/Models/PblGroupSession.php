<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\GroupAnswer;

class PblGroupSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pbl_group_sessions';
    protected $fillable = [
        'user_id',
        'session_code',
        'title',
        'is_active',
        'f1_context',
        'f1_learning_objectives',
        'f3_questions',
        'f5_questions',
        'f4_instruction',
        'f4_question',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'f1_learning_objectives' => 'array',
            'f3_questions' => 'array',
            'f5_questions' => 'array',
        ];
    }

    /**
     * Relasi: Sesi ini dimiliki oleh seorang User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function groupAnswers()
    {
        return $this->hasMany(GroupAnswer::class);
    }
}