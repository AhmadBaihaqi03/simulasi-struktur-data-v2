<?php

namespace App\Models;

use App\Models\IndividualQuestion;
use App\Models\IndividualSubmission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndividualAnswer extends Model
{
	use HasFactory;

	protected $fillable = [
		'individual_submission_id',
		'individual_question_id',
		'answer_given',
		'is_correct',
		'score_earned',
	];

	protected $casts = [
		'answer_given' => 'array',
		'is_correct' => 'boolean',
		'score_earned' => 'integer',
	];

	/**
	 * Relasi ke submission individual.
	 */
	public function individualSubmission(): BelongsTo
	{
		return $this->belongsTo(IndividualSubmission::class);
	}

	/**
	 * Relasi ke pertanyaan individual.
	 */
	public function individualQuestion(): BelongsTo
	{
		return $this->belongsTo(IndividualQuestion::class);
	}
}
