<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogRead extends Model
{
    protected $fillable = ['id', 'log_id', 'user_id', 'is_read', 'read_at', 'created_at', 'updated_at'];

    public function user ()
    {
        return $this->belongsTo (User::class);
    }
}
