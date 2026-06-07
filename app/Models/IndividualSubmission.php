<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndividualSubmission extends Model
{
	use HasFactory;

	protected $fillable = [
		'individual_session_id',
		'student_name',
		'student_number',
		'class_name',
		'total_score',
	];

	protected $casts = [
		'total_score' => 'integer',
	];

	/**
	 * Relasi ke sesi individual.
	 */
	public function individualSession(): BelongsTo
	{
		return $this->belongsTo(IndividualSession::class);
	}

	/**
	 * Relasi ke jawaban-jawaban dari submission ini.
	 */
	public function individualAnswers(): HasMany
	{
		return $this->hasMany(IndividualAnswer::class);
	}
}
