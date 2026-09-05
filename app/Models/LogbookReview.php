<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookReview extends Model
{
    use HasFactory;

    protected $fillable = ['logbook_id', 'reviewer_id', 'reviewer_type', 'comment', 'status'];

    public function logbook(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Logbook::class);
    }

    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
