<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ConfigRepository;
use App\Entities\Config;
use App\Validators\ConfigValidator;

/**
 * Class ConfigRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ConfigRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Config::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return ConfigValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 同步修改配置文件.env add by gui
     * @throws \ErrorException
     */
    public function saveToEnv($key, $value)
    {
        $key      = strtoupper($key);
        $value    = trim($value);
        $env_str  = file_get_contents(base_path('.env'));
        $arr      = explode("\n", $env_str);
        $isChange = false;//是否有改动
        foreach ($arr as $item) {
            if (empty($item)) {
                continue;
            }
            if (!strstr($item, '=')) {
                continue;
            }
            list($env_key, $env_val) = explode('=', $item);

            if ($env_key == $key && $value != $env_val) {
                //替换变更
                $value = trim($value);
                $value = str_replace(' ','', $value);
                $env_str  = str_replace($item, $env_key . '=' . $value, $env_str);
                $isChange = true;
                break;
            }
        }
        if ($isChange === false) {
            //未改动，无需改动
            return true;
        }
        $ret = file_put_contents(base_path('.env'), $env_str);
        if ($ret) {
            return true;
        } else {
            throw new \ErrorException('修改配置失败');
        }
    }
}
