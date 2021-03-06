<?php

namespace backend\models;

use app\models\UserSearch;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WeddingItemOrder;

/**
 * WeddingItemOrderSearch represents the model behind the search form about `common\models\WeddingItemOrder`.
 */
class WeddingItemOrderSearch extends WeddingItemOrder
{
    public $combos;
    public $section_name;
    public $combo_name;
    public $order_sn;
    public $customer_name;
    public $customer_mobile;
    public $project_process;
    public $wedding_date;
    public $wedding_address;
    public $need_item_order = 1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'item_order_id',
                    'order_id',
                    'section_id',
                    'combo_id',
                    'status',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
                'integer',
            ],
            [
                [
                    'custom',
                    'principal',
                    'combos',
                    'section_name',
                    'order_sn',
                    'customer_name',
                    'customer_mobile',
                    'wedding_date',
                    'wedding_address',
                    'need_item_order',
                ],
                'safe',
            ],
            [
                ['deal_price'],
                'number',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'section_name'    => '部门',
            'combo_name'      => '套餐',
            'custom'          => '定制',
            'deal_price'      => '成交价格',
            'status'          => '状态',
            'principal'       => '负责人',
            'order_sn'        => '订单号',
            'customer_name'   => '客户姓名',
            'customer_mobile' => '客户手机号',
            'wedding_date'    => '婚庆日期',
            'wedding_address' => '婚庆地址',
            'need_item_order' => '是否选择当前部门',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $user_info       = UserSearch::getUserInfo(yii::$app->user->identity->getId());
        $user_section_id = $user_info['section'];

        $query = self::find()
            ->alias('wio')
            ->leftJoin(WeddingOrderSearch::tableName() . ' wos', 'wos.order_id=wio.order_id')
            ->leftJoin(WeddingSectionSearch::tableName() . ' wss', 'wss.section_id=wio.section_id')
            ->leftJoin(WeddingComboSearch::tableName() . ' wcs', 'wcs.combo_id=wio.combo_id')
            ->select([
                'wio.*',
                'wos.order_sn',
                'wos.customer_name',
                'wos.project_process',
                'wos.customer_mobile',
                'wos.wedding_date',
                'wos.wedding_address',
                'wcs.combo_name',
            ]);

        if ($user_section_id >= 1)
        {
            $query->where(['wio.section_id' => $user_section_id]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->setSort([
            'attributes' => [
                'order_sn',
                'customer_name',
                'customer_mobile',
                'wedding_date',
                'combo_name',
                'deal_price',
                'status',
                'created_at',
                'updated_at',
            ],
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'customer_name'   => $this->customer_name,
            'order_sn'        => $this->order_sn,
            'customer_mobile' => $this->customer_mobile,
            'wedding_date'    => $this->wedding_date,
            'combo_name'      => $this->combo_name,
            'deal_price'      => $this->deal_price,
            'status'          => $this->status,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ]);

        $query->andFilterWhere([
            'like',
            'custom',
            $this->custom,
        ])
            ->andFilterWhere([
                'like',
                'principal',
                $this->principal,
            ]);

        $query->orderBy(['wedding_date' => SORT_ASC]);

        return $dataProvider;
    }
}
