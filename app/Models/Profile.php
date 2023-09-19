<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'phone_number',
        'first_name',
        'last_name',
        'image',
        'date_of_birth',
        'user_id',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => asset('/storage/profile/' . $image),
        );
    }
    public function followers()
    {
        return $this->hasMany(Followers::class)->where('follow', true);
    }
    public function following()
    {
        return $this->hasMany(Following::class)->where('follow', true);
    }
    public function post()
    {
        return $this->hasMany(Post::class);
    }
}
