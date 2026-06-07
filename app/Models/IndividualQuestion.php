<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndividualQuestion extends Model
{
	use HasFactory;

	protected $fillable = [
		'individual_session_id',
		'type',
		'question_text',
		'options',
		'correct_answer',
		'points',
	];

	protected $casts = [
		'options' => 'array',
		'correct_answer' => 'array',
		'points' => 'integer',
	];

	/**
	 * Relasi ke sesi individual.
	 */
	public function individualSession(): BelongsTo
	{
		return $this->belongsTo(IndividualSession::class);
	}

	/**
	 * Relasi ke jawaban-jawaban untuk pertanyaan ini.
	 */
	public function individualAnswers(): HasMany
	{
		return $this->hasMany(IndividualAnswer::class);
	}
}
