<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class BrandsController extends BaseController
{
    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET'], '/view', [$this, 'view']);
        $app->map(['GET', 'POST'], '/create', [$this, 'create']);
        $app->map(['GET', 'POST'], '/update/[{id}]', [$this, 'update']);
        $app->map(['POST'], '/delete/[{id}]', [$this, 'delete']);
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => ['view', 'create', 'update', 'delete'],
                'users' => ['@'],
            ],
            ['deny',
                'users' => ['*'],
            ],
        ];
    }

    public function view($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        $models = \ExtensionsModel\HandphoneBrandModel::model()->findAll();

        return $this->_container->module->render($response, 'handphones/brands_view.html', [
            'models' => $models
        ]);
    }

    public function create($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        $model = new \ExtensionsModel\HandphoneBrandModel('create');
        $success = false; $message = null;
        if (isset($_POST['HandphoneBrand'])) {
            $model->title = $_POST['HandphoneBrand']['title'];
            $tool = new \Components\Tool();
            $model->slug = $tool->createSlug($model->title);
            // check slug
            $cmodel = \ExtensionsModel\HandphoneBrandModel::model()->findByAttributes(['slug' => $model->slug]);
            if ($cmodel instanceof \RedBeanPHP\OODBBean) {
                $model->slug = $model->slug ."". $cmodel->id;
            }
            $model->description = $_POST['HandphoneBrand']['description'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = $this->_user->id;
            $model->updated_at = date('Y-m-d H:i:s');
            $create = \ExtensionsModel\HandphoneBrandModel::model()->save(@$model);
            if ($create > 0) {
                $message = 'Your data is successfully created.';
                $success = true;
                $id = $model->id;
            } else {
                $message = 'Failed to create new data.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'handphones/brands_create.html', [
            'model' => $model,
            'message' => ($message) ? $message : null,
            'success' => $success,
            'id' => $id
        ]);
    }

    public function update($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        if (empty($args['id']))
            return false;

        $model = \ExtensionsModel\HandphoneBrandModel::model()->findByPk($args['id']);
        $success = false; $message = null;
        if (isset($_POST['HandphoneBrand'])) {
            $model->title = $_POST['HandphoneBrand']['title'];
            if (empty($model->slug)) {
                $tool = new \Components\Tool();
                $model->slug = $tool->createSlug($model->title);
            }
            $model->description = $_POST['HandphoneBrand']['description'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updated_by = $this->_user->id;
            $update = \ExtensionsModel\HandphoneBrandModel::model()->update($model);
            if ($update > 0) {
                $message = 'Your data is successfully updated.';
                $success = true;
                $id = $model->id;
            } else {
                $message = 'Failed to update data.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'handphones/brands_update.html', [
            'model' => $model,
            'message' => ($message) ? $message : null,
            'success' => $success,
        ]);
    }

    public function delete($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = \ExtensionsModel\HandphoneBrandModel::model()->findByPk($args['id']);
        $delete = \ExtensionsModel\HandphoneBrandModel::model()->delete($model);
        if ($delete) {
            $message = 'Your data has been successfully deleted.';
            echo true;
        }
    }
}