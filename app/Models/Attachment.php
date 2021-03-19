<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{

    protected $fillable = ['company_id', 'user_id', 'name', 'path', 'file_md5', 'file_sha1', 'status', 'created_at', 'updated_at'];

    /**
     * 保存附件信息，根据SHA和MD5判断是否重复，重复标记记录status=-1，
     * 由定时任务清理重复附件，释放空间
     * add by gui
     * @param $insArr
     * @return mixed
     */
    public static function addFile ($insArr)
    {
        if (array_get ($insArr, 'user_id', 0) == 0) {
            $insArr['user_id'] = get_login_user_id ();
        }
        $md5  = array_get ($insArr, 'file_md5', '');
        $sha1 = array_get ($insArr, 'file_sha1', '');
        $pic  = Attachment::where ('file_md5', $md5)->where ('file_sha1', $sha1)->first ();
        if (isset($pic->id) && file_exists ($pic->path)) {
            $insArr['status'] = -1;//标记有重复文件存在
            Attachment::create ($insArr);

            return $pic;
        }

        return Attachment::create ($insArr);
    }

}
