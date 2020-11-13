<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class DatabaseBackup.
 * @package namespace App\Entities;
 */
class DatabaseBackup extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'path_file',
        'start_at',
        'end_at',
        'file_size',
        'status',
    ];

    public function statusItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('back_status', $ind, $html);
    }
}
