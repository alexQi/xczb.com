<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17-7-11
 * Time: 下午2:46
 */
namespace frontend\controllers;

use yii;
use yii\web\Controller;
use common\components\MyBehavior;
use yii\web\ForbiddenHttpException;

class BaseController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['MyBehavior'] = [
            'class' => MyBehavior::className(),
            'queryParam' => 'queryParam'
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST==false ? 'testme' : null,
            ],
        ];
    }

    public function init()
    {
        parent::init(); // TODO: 继承父类
    }

    public function beforeAction($action)
    {
        if ($action->getUniqueId()=='site/default/index' && !yii::$app->request->get('code')){
            $realUrl = yii::$app->request->hostInfo.'/'.$action->getUniqueId();
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.yii::$app->params['wechat_appid'].'&redirect_uri='.urlencode($realUrl).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
            return $this->redirect($url);
        }else{
            return parent::beforeAction($action);
        }
    }
}