<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use UsesUuid;

    protected $table = 'universities';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'pt_code',
        'name',
        'address',
        'website'
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
