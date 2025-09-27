<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSocialLink extends Model
{
    protected $fillable = [
        'user_id',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'github',
        'youtube',
    ];


}
