<?php

namespace Extensions\Controllers;

use Components\BaseController as BaseController;

class SeriesController extends BaseController
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
        $app->map(['POST'], '/upload-images', [$this, 'get_upload_images']);
        $app->map(['POST'], '/delete-image/[{id}]', [$this, 'delete_image']);
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => ['view', 'create', 'update', 'delete', 'upload-images', 'delete-image'],
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

        $model = new \ExtensionsModel\HandphoneSeriesModel();
        $models = $model->getData();

        return $this->_container->module->render($response, 'handphones/series_view.html', [
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

        $model = new \ExtensionsModel\HandphoneSeriesModel('create');
        $success = false; $message = null;
        if (isset($_POST['HandphoneSeries'])) {
            $model->title = $_POST['HandphoneSeries']['title'];
            $tool = new \Components\Tool();
            $model->slug = $tool->createSlug($model->title);
            // check slug
            $cmodel = \ExtensionsModel\HandphoneSeriesModel::model()->findByAttributes(['slug' => $model->slug]);
            if ($cmodel instanceof \RedBeanPHP\OODBBean) {
                $model->slug = $model->slug ."". $cmodel->id;
            }
            $model->meta_description = $_POST['HandphoneSeries']['meta_description'];
            $model->description = $_POST['HandphoneSeries']['description'];
            $model->brand_id = $_POST['HandphoneSeries']['brand_id'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = $this->_user->id;
            $model->updated_at = date('Y-m-d H:i:s');
            $create = \ExtensionsModel\HandphoneSeriesModel::model()->save(@$model);
            if ($create > 0) {
                $message = 'Your data is successfully created.';
                $success = true;
                $id = $model->id;
            } else {
                $message = 'Failed to create new data.';
                $success = false;
            }
        }

        $brands = \ExtensionsModel\HandphoneBrandModel::model()->findAll();

        return $this->_container->module->render($response, 'handphones/series_create.html', [
            'model' => $model,
            'brands' => $brands,
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

        $model = \ExtensionsModel\HandphoneSeriesModel::model()->findByPk($args['id']);
        $success = false; $message = null;
        if (isset($_POST['HandphoneSeries'])) {
            $model->title = $_POST['HandphoneSeries']['title'];
            $model->meta_description = $_POST['HandphoneSeries']['meta_description'];
            $model->description = $_POST['HandphoneSeries']['description'];
            $model->brand_id = $_POST['HandphoneSeries']['brand_id'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updated_by = $this->_user->id;
            $update = \ExtensionsModel\HandphoneSeriesModel::model()->update($model);
            if ($update > 0) {
                $message = 'Your data is successfully updated.';
                $success = true;
                $id = $model->id;
            } else {
                $message = 'Failed to update data.';
                $success = false;
            }
        }

        $brands = \ExtensionsModel\HandphoneBrandModel::model()->findAll();
        $images = \ExtensionsModel\HandphoneImagesModel::model()->findAll();

        return $this->_container->module->render($response, 'handphones/series_update.html', [
            'model' => $model,
            'brands' => $brands,
            'images' => $images,
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

        $model = \ExtensionsModel\HandphoneSeriesModel::model()->findByPk($args['id']);
        $delete = \ExtensionsModel\HandphoneSeriesModel::model()->delete($model);
        if ($delete) {
            $message = 'Your data has been successfully deleted.';
            echo true;
        }
    }

    public function get_upload_images($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        if (isset($_POST['HandphoneImages'])) {
            $path_info = pathinfo($_FILES['HandphoneImages']['name']['file_name']);
            if (!in_array($path_info['extension'], ['jpg','JPG','jpeg','JPEG','png','PNG','webp'])) {
                echo json_encode(['status'=>'failed','message'=>'Allowed file type are jpg, png, webp']); exit;
                exit;
            }
            $model = new \ExtensionsModel\HandphoneImagesModel();
            $model->series_id = $_POST['HandphoneImages']['series_id'];
            $model->type = $_POST['HandphoneImages']['type'];
            $model->upload_folder = 'uploads/images/handphones';
            $model->file_name = time().'.'.$path_info['extension'];
            $model->alt = $_POST['HandphoneImages']['alt'];
            $model->description = $_POST['HandphoneImages']['description'];
            $model->created_at = date("Y-m-d H:i:s");
            $create = \ExtensionsModel\HandphoneImagesModel::model()->save(@$model);
            if ($create > 0) {
                $uploadfile = $model->upload_folder . '/' . $model->file_name;
                move_uploaded_file($_FILES['HandphoneImages']['tmp_name']['file_name'], $uploadfile);
                echo json_encode(['status'=>'success','message'=>'Successfully uploaded new images']); exit;
            }
        }

        echo json_encode(['status'=>'failed','message'=>'Unable to upload the files.']); exit;
        exit;
    }

    public function delete_image($request, $response, $args)
    {
        $isAllowed = $this->isAllowed($request, $response);
        if ($isAllowed instanceof \Slim\Http\Response)
            return $isAllowed;

        if(!$isAllowed){
            return $this->notAllowedAction();
        }

        if (!isset($_POST['id'])) {
            return false;
        }

        $model = \ExtensionsModel\HandphoneImagesModel::model()->findByPk($_POST['id']);
        $path = $this->_settings['basePath'].'/../'.$model->upload_folder.'/'.$model->file_name;
        $delete = \ExtensionsModel\HandphoneImagesModel::model()->delete($model);
        if ($delete) {
            if (file_exists($path))
                unlink($path);
            echo true;
        }
        exit;
    }
}