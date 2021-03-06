<?php


namespace console\controllers;

use common\models\RelateActivityApply;
use yii;
use yii\console\Controller;
use common\models\service\ApplyRecordService;

class WorkerController extends Controller
{
    public function actionIndex()
    {
        $ApplyRecordService = new ApplyRecordService();
        $applyUserList = $ApplyRecordService->getAllApplyUserList();
        $redis = yii::$app->redis;

        if (!empty($applyUserList))
        {
            foreach($applyUserList as $user)
            {
                $num = $redis->get('vote_apply_'.$user['id'])?$redis->get('vote_apply_'.$user['id']):0;

                $relateActivityRecord = RelateActivityApply::find()->where(['apply_id'=>$user['id'],'activity_id'=>$user['activity_id']])->one();
                if (empty($relateActivityRecord))
                {
                    $relateActivityRecord = new RelateActivityApply();
                    $relateActivityRecord->activity_id = $user['activity_id'];
                    $relateActivityRecord->apply_id    = $user['id'];
                    $relateActivityRecord->votes       = $num;
                    $relateActivityRecord->created_at  = time();
                    $relateActivityRecord->updated_at  = time();
                }else{
                    $relateActivityRecord->votes       = $num>$relateActivityRecord->votes ? $num : $relateActivityRecord->votes;
                    $relateActivityRecord->updated_at  = time();

                    if ($relateActivityRecord->votes>$num)
                    {
                        $redis->set('vote_apply_'.$relateActivityRecord->apply_id,$relateActivityRecord->votes);
                    }
                }

                $relateActivityRecord->save();
            }
        }
        yii::info('更新用户投票数完成');
        echo "更新完成\n";
    }
}