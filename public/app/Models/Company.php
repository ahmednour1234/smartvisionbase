<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'title_ar',
        'title_en',
        'country',
        'count_vote',
        'link',
        'image',
        'active',
        'regulation',
        'stars',
        'category',
                'orders',
                'number_of_followers',
                'followers_ticktock'

    ];

    protected $casts = [
        'active' => 'boolean',
        'count_vote' => 'integer',
    ];
}
