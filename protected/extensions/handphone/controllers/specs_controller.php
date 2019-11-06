<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class SpecsController extends BaseController
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

        $models = \ExtensionsModel\HandphoneSpecsModel::model()->findAll();

        return $this->_container->module->render($response, 'handphones/specs_view.html', [
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

        $model = new \ExtensionsModel\HandphoneSpecsModel('create');
        $success = false; $message = null;
        if (isset($_POST['HandphoneSpecs'])) {
            $model->title = $_POST['HandphoneSpecs']['title'];
            $model->description = $_POST['HandphoneSpecs']['description'];
            $model->type = $_POST['HandphoneSpecs']['type'];
            $model->unit = $_POST['HandphoneSpecs']['unit'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $create = \ExtensionsModel\HandphoneSpecsModel::model()->save(@$model);
            if ($create > 0) {
                $message = 'Your data is successfully created.';
                $success = true;
                $id = $model->id;
            } else {
                $message = 'Failed to create new data.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'handphones/specs_create.html', [
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

        $model = \ExtensionsModel\HandphoneSpecsModel::model()->findByPk($args['id']);
        $success = false; $message = null;
        if (isset($_POST['HandphoneSpecs'])) {
            $model->title = $_POST['HandphoneSpecs']['title'];
            $model->description = $_POST['HandphoneSpecs']['description'];
            $model->type = $_POST['HandphoneSpecs']['type'];
            $model->unit = $_POST['HandphoneSpecs']['unit'];
            $model->updated_at = date('Y-m-d H:i:s');
            $update = \ExtensionsModel\HandphoneSpecsModel::model()->update($model);
            if ($update > 0) {
                $message = 'Your data is successfully updated.';
                $success = true;
                $id = $model->id;
            } else {
                $message = 'Failed to update data.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'handphones/specs_update.html', [
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

        $model = \ExtensionsModel\HandphoneSpecsModel::model()->findByPk($args['id']);
        $delete = \ExtensionsModel\HandphoneSpecsModel::model()->delete($model);
        if ($delete) {
            $message = 'Your data has been successfully deleted.';
            echo true;
        }
    }
}