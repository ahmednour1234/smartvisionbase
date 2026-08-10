<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  use HasFactory;

  protected $fillable = [
    'name_ar',
    'name_en',
    'description_ar',
    'description_en',
    'event_date',
    'address_ar',
    'address_en',
    'location',
    'attendees_limit',
    'main_image',
    'active'
  ];
}
