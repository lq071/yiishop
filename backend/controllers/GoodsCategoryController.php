<?php

namespace backend\controllers;

use backend\models\GoodsCategory;

class GoodsCategoryController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        $rows = GoodsCategory::find()->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //添加
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new GoodsCategory();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);
                return $this->redirect(['goods-category/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $model = GoodsCategory::findOne(['id'=>$id]);
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);
                return $this->redirect(['goods-category/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //删除
    public function actionDelete($id){
/*        $model =GoodsCategory::findOne(['id'=>$id]);

        $model->save();
        echo json_encode(1);*/
    }
}
