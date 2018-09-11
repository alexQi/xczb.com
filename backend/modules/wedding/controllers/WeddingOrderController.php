<?php

namespace backend\modules\wedding\controllers;


use common\models\WeddingCombo;
use Yii;
use common\models\WeddingOrder;
use backend\models\WeddingItemOrderSearch;
use backend\models\WeddingComboSearch;
use backend\models\WeddingSectionSearch;
use backend\models\WeddingOrderSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WeddingOrderController implements the CRUD actions for WeddingOrder model.
 */
class WeddingOrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all WeddingOrder models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new WeddingOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WeddingOrder model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $item_data_model = WeddingItemOrderSearch::find()
            ->alias('wios')
            ->leftJoin(WeddingCombo::tableName() . ' wc', 'wc.combo_id=wios.combo_id')
            ->leftJoin(WeddingSectionSearch::tableName() . 'wss', 'wss.section_id=wios.section_id')
            ->where(['order_id' => $id])
            ->select('wios.*,wc.combo_name,wss.section_name')
            ->all();

        return $this->render('view', [
            'model'           => $model,
            'item_data_model' => $item_data_model,
        ]);
    }

    /**
     * Creates a new WeddingOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WeddingOrderSearch();

        if ($model->load(Yii::$app->request->post()))
        {
            $tran = yii::$app->db->beginTransaction();
            try
            {
                $model->wedding_date = strtotime($model->wedding_date);
                $model->order_sn     = 'ON' . time() . rand(1000, 9999);
                $model->user_id      = yii::$app->user->id;
                $model->created_at   = time();
                $model->updated_at   = time();

                if (!$model->save())
                {
                    throw new HttpException('下单失败');
                }

                foreach (Yii::$app->request->post('WeddingItemOrderSearch') as $item)
                {
                    if ($item['need_item_order']==1)
                    {
                        continue;
                    }
                    $item_order_model = new WeddingItemOrderSearch();

                    $temp_array['WeddingItemOrderSearch'] = $item;
                    $item_order_model->load($temp_array);
                    $item_order_model->order_id   = $model->order_id;
                    $item_order_model->user_id    = yii::$app->user->id;
                    $item_order_model->status     = 0;
                    $item_order_model->created_at = time();
                    $item_order_model->updated_at = time();
                    if (!$item_order_model->save())
                    {
                        throw new HttpException('写入子订单失败');
                    }
                }
                $tran->commit();
            } catch (HttpException $e)
            {
                $tran->rollBack();
                throw new HttpException($e->getMessage());
            }
            return $this->redirect([
                'view',
                'id' => $model->order_id,
            ]);
        }
        else
        {
            $sections_model = WeddingSectionSearch::find()->all();

            $item_data_model = [];
            foreach ($sections_model as $key => $section)
            {
                $all_combos = WeddingComboSearch::find()->where(['section_id' => $section->section_id])->select([
                    'combo_id',
                    'combo_name',
                ])->asArray()->all();
                array_unshift($all_combos, [
                    'combo_id'   => -1,
                    'combo_name' => '无套餐',
                ]);

                $item_order_model = new WeddingItemOrderSearch();

                $item_order_model->section_id      = $section->section_id;
                $item_order_model->section_name    = $section->section_name;
                $item_order_model->combos          = $all_combos;
                $item_order_model->need_item_order = 1;
                $item_data_model[]                 = $item_order_model;
            }

            $model->wedding_date = date('Y-m-d', time() + 3 * 86400);
            return $this->renderAjax('create', [
                'model'           => $model,
                'item_data_model' => $item_data_model,
            ]);
        }
    }

    /**
     * Updates an existing WeddingOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()))
        {
            $tran = yii::$app->db->beginTransaction();
            try
            {
                $model->wedding_date = strtotime($model->wedding_date);
                $model->updated_at   = time();

                if (!$model->save())
                {
                    throw new HttpException('下单失败');
                }

                foreach (Yii::$app->request->post('WeddingItemOrderSearch') as $item)
                {
                    if ($item['need_item_order'] == 1)
                    {
                        WeddingItemOrderSearch::deleteAll([
                            'section_id' => $item['section_id'],
                            'order_id'   => $id,
                        ]);
                        continue;
                    }
                    $item_order_model = WeddingItemOrderSearch::find()->where([
                        'section_id' => $item['section_id'],
                        'order_id'   => $id,
                    ])->one();
                    if (!$item_order_model)
                    {
                        $item_order_model = new WeddingItemOrderSearch();
                    }
                    $temp_array['WeddingItemOrderSearch'] = $item;
                    $item_order_model->load($temp_array);
                    if ($item_order_model->isNewRecord)
                    {
                        $item_order_model->order_id   = $model->order_id;
                        $item_order_model->user_id    = yii::$app->user->id;
                        $item_order_model->status     = 0;
                        $item_order_model->created_at = time();
                    }
                    $item_order_model->updated_at = time();
                    if (!$item_order_model->save())
                    {
                        throw new HttpException('更新子订单失败');
                    }
                }
                $tran->commit();
            } catch (HttpException $e)
            {
                $tran->rollBack();
                throw new HttpException($e->getMessage());
            }
            return $this->redirect([
                'view',
                'id' => $model->order_id,
            ]);
        }
        else
        {
            $sections_model = WeddingSectionSearch::find()->all();

            $item_data_model = [];
            foreach ($sections_model as $key => $section)
            {
                $item_order_model = WeddingItemOrderSearch::find()->where([
                    'section_id' => $section->section_id,
                    'order_id'   => $id,
                ])->one();
                if (!$item_order_model)
                {
                    $item_order_model = new WeddingItemOrderSearch();
                }

                $all_combos = WeddingComboSearch::find()->where(['section_id' => $section->section_id])->select([
                    'combo_id',
                    'combo_name',
                ])->asArray()->all();
                array_unshift($all_combos, [
                    'combo_id'   => -1,
                    'combo_name' => '无套餐',
                ]);

                $item_order_model->section_id      = $section->section_id;
                $item_order_model->section_name    = $section->section_name;
                $item_order_model->combos          = $all_combos;
                $item_order_model->need_item_order = $item_order_model->isNewRecord ? 1 : 2;
                $item_data_model[]                 = $item_order_model;
            }
            $model->wedding_date = date('Y-m-d', $model->wedding_date);
            return $this->renderAjax('update', [
                'model'           => $model,
                'item_data_model' => $item_data_model,
            ]);
        }
    }

    /**
     * Deletes an existing WeddingOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        WeddingItemOrderSearch::deleteAll([
            'order_id' => $id,
        ]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the WeddingOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return WeddingOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WeddingOrder::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the WeddingOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return WeddingItemOrderSearch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findItemModel($id)
    {
        if (($model = WeddingItemOrderSearch::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
