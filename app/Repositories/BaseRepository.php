<?php
/**
 * 基础业务类
 * Created by PhpStorm.
 * User: gui
 * Date: 2020/3/12
 */

namespace App\Repositories;

use App\Exceptions\BusinessException;
use App\Validators\BaseValidator;
use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    /**
     * @var Application
     */
    protected $app;
    protected $model;
    /**
     * @var BaseValidator
     */
    protected $validator;

    public function __construct (Application $app)
    {
        $this->app = $app;
        $this->makeModel ();
        $this->makeValidator ();
    }

    /**
     * add by gui
     * @return Model|mixed
     * @throws BusinessException
     */
    public function makeModel ()
    {
        $model = $this->app->make ($this->model ());

        if (!$model instanceof Model) {
            throw new BusinessException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     *  add by gui
     * @return BaseValidator|mixed
     * @throws BusinessException
     */
    public function makeValidator ()
    {
        $validator = $this->app->make ($this->validator ());
        if (!$validator instanceof BaseValidator) {
            throw new BusinessException("Class {$this->validator()} must be an instance of App\\Validators\\BaseValidator");
        }

        return $this->validator = $validator;
    }

    /**
     * add by gui
     * @param $id
     * @return mixed
     */
    public function find ($id)
    {
        return $this->model->find ($id);
    }

    /**
     * add by gui
     * @param array $attributes
     * @return mixed
     */
    public function create (array $attributes)
    {
        return $this->model->create ($attributes);
    }

    /**
     * add by gui
     * @param array $attributes
     * @param       $id
     * @return mixed
     */
    public function update (array $attributes, $id)
    {

        $model = $this->model->findOrFail ($id);
        $model->fill ($attributes);
        $model->save ();

        return $model;
    }

    /**
     * add by gui
     * @param $id
     * @return mixed
     */
    public function delete ($id)
    {
        $model = $this->find ($id);

        return $model->delete ();
    }

    /**
     * 检查是否有记录是否有权限权限 add by gui
     * @param array|object|integer $row 记录值或主键ID
     * @return bool
     * @throws BusinessException
     */
    public function checkAuth ($row = null)
    {
        if (is_numeric ($row)) {
            $row = $this->find ($row);
        }

        return true;
    }

    /**
     *  add by gui
     * 检查是否有记录是否有权限权限 add by gui
     * @param array|object|integer $row 记录值或主键ID
     * @return bool
     */
    public function checkAuthToAbort ($row = null)
    {
        try {
            return $this->checkAuth ($row);
        } catch (BusinessException $e) {
            abort (403, $e->getMessage ());
        }
    }

}
