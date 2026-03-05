<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'postal_code',
        'address',
        'building',
        'profile_image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * プロフィール画像URL取得
     */
    public function getImageUrlAttribute()
    {
    return $this->profile_image
        ? asset('storage/' . $this->profile_image)
        : asset('images/default.png');
    }
}
