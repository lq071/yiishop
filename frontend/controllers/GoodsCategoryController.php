<?php

namespace frontend\controllers;


use frontend\models\GoodsCategory;

class GoodsCategoryController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        $rows = GoodsCategory::find()->all();// 这里的where条件需要加入主表表明来区别所属表

       var_dump($rows); exit;
        return $this->render('site',['rows'=>$rows]);
    }
}
