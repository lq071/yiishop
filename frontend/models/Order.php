<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property string $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    public $address_id;
    public $logos = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }
    //判断 状态 显示在前台
    public function getStatus($status){
        if($status==0){
            return '已取消';
        }elseif($status==1){
            return '待付款';
        }elseif($status==2){
            return '待发货';
        }elseif($status==3){
            return '待收货';
        }elseif($status==4){
            return '完成';
        }
    }
    public static $delivery = [
            1=>['顺丰快递',25,'快'],
            2=>['EMS',25 ,'快'],
            3=>['圆通快递',10,'一般'],
            4=>['中通快递',10,'一般'],
        ];
    public static $payment = [
        1=>['在线支付' ,'即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        2=>['货到付款','送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        3=>['上门自提','自提时付款，支持现金、POS刷卡、支票支付'],
        4=>['邮局汇款','通过快钱平台收款 汇款后1-3个工作日到账'],
    ];
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'status',], 'integer'],
            [['name', 'province', 'city', 'area', 'address', 'tel','address_id','delivery_id','payment_id', ], 'required'],
            [['delivery_price', 'total'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['province', 'city', 'area'], 'string', 'max' => 20],
            [['address', 'delivery_name', 'payment_name', 'trade_no'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'name' => 'Name',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'address' => 'Address',
            'tel' => 'Tel',
            'delivery_id' => 'Delivery ID',
            'delivery_name' => 'Delivery Name',
            'delivery_price' => 'Delivery Price',
            'payment_id' => 'Payment ID',
            'payment_name' => 'Payment Name',
            'total' => 'Total',
            'status' => 'Status',
            'trade_no' => 'Trade No',
            'create_time' => 'Create Time',
        ];
    }
}
