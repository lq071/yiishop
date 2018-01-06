<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $label
 * @property string $url
 * @property integer $parent_id
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

/*    public function getMenu(){
        return $this->hasMany(self::className(),['parent_id'=>'id']);
    }*/
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label', 'parent_id'], 'required'],
            [['label'], 'string', 'max' => 20],
            ['url','string'],
            //['url','checkUrl'],
        ];
    }
/*    public function checkUrl(){
        //var_dump(11);exit;
        if($this->parent_id !=0 ){
            $this->addError('url','请选择路由');
        }
    }*/

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'label' => '菜单名称',
            'url' => '路由',
            'parent_id' => '上级分类',
        ];
    }
}
