<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryCertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'industry_id',
        'background_image',
        'signatory_name',
        'signatory_position',
        'seal_image',
    ];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
