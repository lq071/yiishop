<?php

namespace backend\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;

/**
 * This is the model class for table "goods_category".
 *
 * @property integer $id
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property integer $parent_id
 * @property string $intro
 */
class GoodsCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','parent_id'],'required'],
            [['parent_id'], 'integer'],
            [['intro'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['parent_id'],'checkPid'],
        ];
    }

    public function checkPid(){
        if(!$this->parent_id==0){
            if($this->parent_id==$this->id){
                $this->addError('parent_id','不能修改到自己的分类下');
            }
            $parent = GoodsCategory::findOne(['id'=>$this->parent_id]);
            if($parent->isChildOf($this)){
                $this->addError('parent_id','不能修改为自己的子孙节点');
            }
        }

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tree' => 'Tree',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'name' => '名称',
            'parent_id' => '上级分类id',
            'intro' => '简介',
        ];
    }


    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
               // 'leftAttribute' => 'lft',
               // 'rightAttribute' => 'rgt',
               // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new GoodsCategoryQuery(get_called_class());
    }
    public static function getNodes(){

        $node = self::find()->select(['id','name','parent_id'])->asArray()->all();
        array_unshift($node,['id'=>0,'parent_id'=>0,'name'=>'顶级分类']);
        //$nodes[]=['id'=>0,'parent_id'=>0,'name'=>'顶级分类'];
        return json_encode($node);
    }
}
