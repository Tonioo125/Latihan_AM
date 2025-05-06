<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class UserFile extends Model
{
    use UsesUuid;

    protected $table = 'user_files';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'file_category_id',
        'file_url',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fileCategory()
    {
        return $this->belongsTo(FileCategory::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->file_url ? asset('storage/' . $this->file_url) : null;
    }
}
