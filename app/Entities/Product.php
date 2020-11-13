<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Product.
 *
 * @package namespace App\Entities;
 */
class Product extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cate_id','title','standard_no','model','shelf_life','content','video_url','card_background','sort','warehouse','unit','is_develop_member','status'
    ];

    public static function showName ($id)
    {
        $info = self::find($id);
        return $info->title ?? '';
    }

    public function statusItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('product_status', $ind, $html);
    }

    public function isDevelopMemberItem ($ind = 'all', $html = false)
    {
        return get_item_parameter ('is_develop_member', $ind, $html);
    }

    public function cate ()
    {
        return $this->hasOne (ProductCate::class,'id','cate_id');
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class)->orderBy('price','ASC');
    }
}
