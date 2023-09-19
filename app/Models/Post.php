<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\comments;
class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'images',
        'caption',
        'profile_id',
    ];

    protected function images(): Attribute
    {
        return Attribute::make(
            get: fn ($images) => asset('/storage/post/' . $images),
        );
    }

    public function comments()
    {
        return $this->hasMany(comments::class);
    }

    public function like()
    {
        return $this->hasMany(Like::class)->where('like', true);
    }
}
