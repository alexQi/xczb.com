<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17-7-11
 * Time: 下午2:46
 */
namespace restful\controllers;

use yii;
use yii\web\Response;
use yii\rest\Controller;
use yii\filters\ContentNegotiator;

class BaseController extends Controller
{
    protected $state    = '0';
    protected $message  = 'failed';
    protected $data     = [];
    protected $getData  = [];
    protected $postData = [];

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @return array
     */
    public function behaviors(){
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }

    /**
     * init data
     */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->getData  = yii::$app->request->get();
        $this->postData = yii::$app->request->post();
    }

    /**
     * @param $action
     * @param $result
     * @return array|mixed
     */
    public function afterAction($action, $result)
    {
        $dataArray = parent::afterAction($action, $result); // TODO: Change the autogenerated stub
        $response = [
            'state'   => $this->state,
            'message' => $this->message,
        ];
        if (!empty($dataArray))
        {
            $response['data'] = $dataArray;
        }
        return $response;
    }
}
