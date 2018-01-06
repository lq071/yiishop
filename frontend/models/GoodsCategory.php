<?php

namespace frontend\models;

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


    // 关联获取子分类
    public function getSubCategory()
    {
        return $this->hasMany(GoodsCategory::className(), ['parent_id' => 'id'])->from(GoodsCategory::tableName().' cate');// from设置别名，尽量避免手写表名称，会要求手动添加表前缀
    }
}
