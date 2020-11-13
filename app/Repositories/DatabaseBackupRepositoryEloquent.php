<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\DatabaseBackupRepository;
use App\Entities\DatabaseBackup;
use App\Validators\DatabaseBackupValidator;

/**
 * Class DatabaseBackupRepositoryEloquent.
 * @package namespace App\Repositories;
 */
class DatabaseBackupRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model()
    {
        return DatabaseBackup::class;
    }

    /**
     * Specify Validator class name
     * @return mixed
     */
    public function validator()
    {

        return DatabaseBackupValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 检查是否备份成功 add by gui
     * @param null $id
     * @return bool
     */
    public function checkBakSuccess($id = null)
    {
        if (empty($id)) return false;
        $info = $this->find($id);
        if (empty($info)) return false;
        $file   = $info->path_file . '/' . $info->name;
        $exists = Storage::exists($file);
        if (!$exists) {
            //文件不存在
            if (strtotime($info->start_at) <= strtotime(' - 1 hour')) {
                //更新失败
                $this->update([
                    'status' => 3,
                ], $id);
            }

            return false;
        }
        $size = Storage::size($file);
        $time = Storage::lastModified($file);

        if ($size && $time) {
            $ent_at = date('Y-m-d H:i:s', $time);
            //更新成功
            $this->update([
                'file_size' => $size,
                'end_at'    => $ent_at,
                'status'    => 1,
            ], $id);
        }
    }

}
