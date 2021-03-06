<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2018/4/24
 * Time: 23:22
 */

namespace console\controllers;

use yii;
use common\components\Common;
use common\models\Video;
use console\models\Tencent;
use yii\console\Controller;

class TencentController extends Controller {

    /**
     * @throws yii\base\Exception
     * @throws yii\db\Exception
     */
    public function actionIndex() {
        $model       = new Tencent();
        $res         = $model->getAccessToken();
        $jsonData    = json_decode($res, true);
        $accessToken = $jsonData['data']['access_token'];
        $apiUrl      = "https://api.om.qq.com/article/clientlist?access_token=";
        $apiUrl      = $apiUrl . $accessToken;
        $page        = 1;
        $tempIndex = [];
        while (true) {
            $requstUrl  = $apiUrl . "&page=$page&limit=10";
            $result     = Common::httpRequest($requstUrl);
            $tempData   = json_decode($result, true);
            if (!isset($tempData['data']) || !isset($tempData['data']['total'])){
                echo date('Y-m-d H:i:s',time())." ".$result."\r\n";
                break;
            }
            $totalVideo = $tempData['data']['total'];
            $totalPage  = intval($totalVideo / 10);
            $remainder  = $totalVideo % 10;
            if ($remainder != 0) {
                $totalPage += 1;
            }
            $data = [];
            foreach ($tempData['data']['articles'] as $key => $val) {
                if (!isset($val['article_video_info']) || !isset($val['article_video_info']['vid']) || $val['article_video_info']['vid']==''){
                    break;
                }
                $tempIndex[] = $val['article_video_info']['vid'];
                $row = Video::findOne(['video_url'=>$val['article_video_info']['vid']]);
                if ($row){
                    continue;
                }
                $data[] = [
                    'video_cate_id' => 1,
                    'video_name'    => $val['article_video_info']['title'],
                    'video_url'     => $val['article_video_info']['vid'],
                    'poster'        => $val['article_imgurl'],
                    'play_num'      => rand(0, 99999),
                    'like_num'      => rand(0, 99999),
                    'uploader'      => '维尔斯',
                    'video_time'    => '- : -',
                    'created_at'    => $val['article_pub_time'],
                    'updated_at'    => $val['article_pub_time']
                ];
            }
            yii::$app->db->createCommand()
                ->batchInsert(Video::tableName(), ['video_cate_id', 'video_name', 'video_url', 'poster', 'play_num', 'like_num', 'uploader', 'video_time', 'created_at', 'updated_at'],
                    $data)
                ->execute();

            if ($page == $totalPage) {
                break;
            }
            $page++;
        }
        if (!empty($tempIndex)){
            yii::$app->db->createCommand()->update(Video::tableName(),['status'=>1],['not in','video_url',$tempIndex]);
            echo date('Y-m-d H:i:s',time())." handle data success \r\n";
        }else{
            echo date('Y-m-d H:i:s',time())." there is no new data \r\n";
        }
    }
}