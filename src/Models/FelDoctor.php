<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FelDoctor extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'fel_doctors';

    protected $guarded = [];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
