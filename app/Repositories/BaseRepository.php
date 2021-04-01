<?php
/**
 * 基础业务类
 * Created by PhpStorm.
 * User: gui
 * Date: 2020/3/12
 */

namespace App\Repositories;

use App\Exceptions\BusinessException;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    /**
     * @var Application
     */
    protected $app;
    protected $model;

    public function __construct (Application $app)
    {
        $this->app = $app;
        $this->makeModel ();
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
}
