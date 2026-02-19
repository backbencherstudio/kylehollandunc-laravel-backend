<?php

namespace App\Models\ProfileSetting;

use Illuminate\Database\Eloquent\Model;

class ProfileSetting extends Model
{
    protected $fillable = ['user_id', 'phone', 'country', 'state'];
}
