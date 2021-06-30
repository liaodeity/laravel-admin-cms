<?php
/*
|-----------------------------------------------------------------------------------------------------------
| laravel-admin-cms [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( MIT )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2020-2021 https://gitee.com/liaodeiy/laravel-admin-cms All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/

namespace App\Repositories;

use App\Exceptions\BusinessException;
use App\Validators\BaseValidator;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;

/**
 * 基础业务类
 * Class BaseRepository
 * @package App\Repositories
 */
class BaseRepository implements InterfaceRepository
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var Model
     */
    protected $model;
    /**
     * @var BaseValidator
     */
    protected $validator;

    public function __construct (Application $app)
    {
        $this->app = $app;
        $this->makeModel ();
    }

    /**
     * add by gui
     * @return Model|mixed
     * @throws BusinessException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
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
     * 模型类 add by gui
     */
    public function model ()
    {

    }

    /**
     * add by gui
     * @param null $validator
     * @return Model|mixed
     * @throws BusinessException
     */
    public function makeValidator ($validator = null): BaseValidator
    {
        if (is_null ($validator)) {
            $validator = $this->validator ();
        }
        try {
            $validator = $this->app->make ($validator);
        } catch (BindingResolutionException $e) {
            throw new BusinessException($e->getMessage ());
        }

        if (!$validator instanceof BaseValidator) {
            throw new BusinessException("Class {$validator} must be an instance of App\\Validators\\LiaoValidator");
        }

        return $this->validator = $validator;
    }

    /**
     * 表单认证类 add by gui
     */
    public function validator ()
    {

    }

    /**
     * add by gui
     * @param array $attributes
     * @return mixed
     */
    public function create (array $attributes)
    {
        $attributes = $this->formatRequestInput ($attributes, __FUNCTION__);

        return $this->model->create ($attributes);
    }

    public function formatRequestInput (array $input, $type = null)
    {
        return $input;
    }

    /**
     * add by gui
     * @param array $attributes
     * @param       $id
     * @return mixed
     */
    public function update (array $attributes, $id)
    {
        $attributes = $this->formatRequestInput ($attributes, __FUNCTION__);
        $model      = $this->model->findOrFail ($id);
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
     * add by gui
     * @param $id
     * @return mixed
     */
    public function find ($id)
    {
        return $this->model->find ($id);
    }

    /**
     * 是否允许删除 add by gui
     * @param $id
     * @return bool
     */
    public function allowDelete ($id)
    {
        return true;
    }
}
