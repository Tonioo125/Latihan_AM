<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileCategory extends Model
{
    use UsesUuid;

    protected $table = 'file_categories';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'image_url'
    ];

    protected $casts = [
        'id' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            if ($category->icon && Storage::exists($category->icon)) {
                Storage::delete($category->icon);
            }
        });
    }

    public function files()
    {
        return $this->hasMany(UserFile::class);
    }
}
