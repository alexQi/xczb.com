<?php

namespace frontend\modules\ajax\controllers;

use yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * 公共控制器
 */
class BaseController extends Controller{

    public $enableCsrfValidation = false;

    protected $ajaxReturn = [];
    protected $postData  = [];
    protected $getData   = [];

    public function init(){
        //Format IO data
        yii::$app->response->format = Response::FORMAT_JSON;

        $this->ajaxReturn = ['state' => 0, 'message' => '未知错误'];
        $this->postData   = Yii::$app->request->post();
        $this->getData    = Yii::$app->request->get();
    }

}
