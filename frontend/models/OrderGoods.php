<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order_goods".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $goods_id
 * @property string $goods_name
 * @property string $logo
 * @property string $price
 * @property integer $amount
 * @property string $total
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'amount'], 'integer'],
            [['price', 'total'], 'number'],
            [['goods_name', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'goods_id' => 'Goods ID',
            'goods_name' => 'Goods Name',
            'logo' => 'Logo',
            'price' => 'Price',
            'amount' => 'Amount',
            'total' => 'Total',
        ];
    }
}
