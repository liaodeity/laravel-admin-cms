<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = ['site_name', 'company_name','sort_name', 'site_url', 'contact_name', 'telephone', 'email', 'fax', 'address', 'map_card_url', 'icp', 'seo_title', 'seo_keyword', 'seo_description','watermark_text', 'created_at', 'updated_at'];

    public static function getConfig ()
    {
        $config = Config::find (1);

        return $config;
    }
}
