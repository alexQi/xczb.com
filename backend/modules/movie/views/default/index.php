<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\VideoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '视频列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <!--取消同步,新增视频-->
                <div class="box-tools">
                    <?= Html::a('添加视频', ['create'], ['class' => 'btn btn-success']) ?>
                </div>
            </div>

            <div class="box-body">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'layout'       => "{items}{summary}{pager}",
                    'summary'      => "<span class='dataTables_info'>当前共有{totalCount}条数据,分为{pageCount}页,当前为第{page}页</span>",
                    'options'      => [
                        'class' => 'col-sm-12 no-padding'
                    ],
                    'pager' => [
                        'options'=>[
                            'class' => 'pagination pull-right no-margin',
                        ]
                    ],
                    'columns' => [
                        [
                            'attribute'=>'video_id',
                            "headerOptions" => [
                                "width" => "80"
                            ],
                        ],
                        [
                            'label' => '视频分类',
                            'attribute'=>'video_cate_id',
                            'format' => 'html',
                            'value'=>function ($model) {
                                $cateList = \common\models\Video::getCategoryList();
                                return  $cateList[$model->video_cate_id];
                            },
                            'filter' => \common\models\Video::getCategoryList(), //筛选的数据
                            "headerOptions" => [
                                "width" => "120"
                            ],
                        ],
                        [
                            'label' => '排序',
                            'attribute'=>'sort',
                            "headerOptions" => [
                                "width" => "100"
                            ],
                        ],
                        [
                            'attribute'=>'video_name',
                            "headerOptions" => [
                                "width" => "250"
                            ],
                        ],
                        [
                            'label' => '状态',
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => function($model) {
                                $string = $model->status==1 ? '禁用' : '启用';
                                $class  = $model->status==1 ? 'danger' : 'success';
                                $html   ='<span class="label label-'.$class.'">'.$string.'</span>';
                                return $html;
                            },
                            'filter' => [1=>'禁用',2=>'启用'], //筛选的数据
                            "headerOptions" => [
                                "width" => "80"
                            ],
                        ],
                        [
                            'attribute'=>'created_at',
                            "headerOptions" => [
                                "width" => "150"
                            ],
                        ],
                        'uploader',
                        [
                            'class' => 'backend\components\LauaoActionColumn',
                            'template' => '{view} {update} {delete}',
                            "headerOptions" => [
                                "width" => "150"
                            ],
                        ],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>
