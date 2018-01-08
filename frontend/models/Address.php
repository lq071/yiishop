<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $tel
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property integer $status
 * @property integer $is_default
 * @property integer $create_time
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','province','city','area','tel'],'required'],
            [['member_id', 'status', 'is_default','create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['tel'], 'string', 'max' => 11],
            [['address'], 'string', 'max' => 100],
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
            'tel' => 'Tel',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'address' => 'Address',
            'status' => 'Status',
            'is_default' => 'Is Default',
            'create_time' => 'Create Time',
        ];
    }
}
