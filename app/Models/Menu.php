<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['pid', 'type', 'title', 'auth_name', 'href', 'icon', 'target', 'is_shortcut', 'status', 'created_at', 'updated_at'];
}
