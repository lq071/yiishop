<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "Brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property string $logo
 * @property integer $sort
 * @property integer $status
 */
class Brand extends \yii\db\ActiveRecord
{
    //public $imgFile;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','sort','status','logo'], 'required'],
            [['intro'], 'safe'],
            [['sort'], 'integer'],
            [['name'], 'string', 'max' => 50],
           // ['imgFile','file','extensions'=>['jpg','png','gif'],'maxSize'=>1024*1024,'skipOnEmpty'=>true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            'logo' => 'LOGO图片',
            'sort' => '排序',
            'status' => '状态',
            'imgFile' => 'LOGO',
        ];
    }
}
