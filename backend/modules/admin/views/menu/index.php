<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\modules\admin\models\searchs\Menu */

$this->title                   = Yii::t('rbac-admin', 'Menus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?=Html::encode($this->title)?></h3>
                <div class="box-tools">
                    <?=Html::a(
                        '<i class="fa fa-plus"></i> ' . Yii::t('rbac-admin', 'Create Menu'),
                        ['create'],
                        [
                            'class'       => 'btn btn-sm btn-info detail-link',
                            'data-key'    => '',
                            'data-toggle' => 'modal',
                            'data-target' => '#activity-modal',
                        ]
                    )?>
                </div>
            </div>
            <div class="box-body">
                <?php Pjax::begin(); ?>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel'  => $searchModel,
                    'layout'       => "{items}{summary}{pager}",
                    'summary'      => "<span class='dataTables_info'>当前共有{totalCount}条数据,分为{pageCount}页,当前为第{page}页</span>",
                    'options'      => [
                        'class' => 'col-sm-12 no-padding'
                    ],
                    'pager'        => [
                        'options' => [
                            'class' => 'pagination pull-right no-margin',
                        ]
                    ],
                    'columns'      => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            'label'          => '图标',
                            'attribute'      => 'data',
                            'format'         => 'raw',
                            'value'          => function ($model) {
                                $data = json_decode($model->data, true);
                                if (isset($data['icon'])) {
                                    $html = '<i class="fa fa-' . $data['icon'] . '"></i>';
                                } else {
                                    $html = '<i class="fa fa-circle-o"></i>';
                                }
                                return $html;
                            },
                            "headerOptions"  => [
                                "width" => "80",
                                'class' => 'text-center'
                            ],
                            "contentOptions" => [
                                'class' => 'text-center'
                            ]
                        ],
                        [
                            'attribute'     => 'name',
                            "headerOptions" => [
                                "width" => "150"
                            ],
                        ],
                        [
                            'attribute'     => 'menuParent.name',
                            'filter'        => Html::activeTextInput($searchModel, 'parent_name', [
                                'class' => 'form-control', 'id' => null
                            ]),
                            'label'         => Yii::t('rbac-admin', 'Parent'),
                            "headerOptions" => [
                                "width" => "120"
                            ],
                        ],
                        [
                            'attribute'     => 'route',
                            "headerOptions" => [
                                "width" => "300"
                            ],
                        ],
                        [
                            'attribute'     => 'order',
                            "headerOptions" => [
                                "width" => "100"
                            ],
                        ],
                        [
                            'class'         => 'backend\components\LauaoActionColumn',
                            'template'      => '{view} {update} {delete}',
                            'buttons'       => [
                                'update' => function ($url, $model) {
                                    $options = [
                                        'class'       => 'btn btn-sm margin-r-5 bg-purple detail-link',
                                        'title'       => Yii::t('rbac-admin', 'Update'),
                                        'data-pjax'   => "0",
                                        'data-key'    => $model->name,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#activity-modal',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                                }
                            ],
                            "headerOptions" => [
                                "width" => "150"
                            ],
                        ],
                    ],
                ]);
                ?>
                <?php Pjax::end(); ?>
                <?php Modal::begin([
                    'id'     => 'activity-modal',
                    'header' => '<h4 class="modal-title"><i class="glyphicon glyphicon-transfer"></i> ITEM MANAGER</h4>',
                    'size'   => Modal::SIZE_LARGE,
                ]); ?>
                <?php Modal::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs(
    "
        $(document).on(\"click\",\".detail-link\",function() {
            $.get($(this).attr(\"href\"),
                function (data) {
                    $('#activity-modal .modal-body').html(data);
                    $('#activity-modal').modal();
                }
            );
        });
    "
);
?>
