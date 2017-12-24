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
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($model->parent_id){
                    $model->appendTo($parent);
                }else{//
                    $model->makeRoot();
                }

                \Yii::$app->session->setFlash('success','添加成功');
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
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($model->parent_id){
                    $model->appendTo($parent);
                }else{
                    $model->makeRoot();
                }
                \Yii::$app->session->setFlash('success','更新成功');
                return $this->redirect(['goods-category/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //删除
    public function actionDelete($id){
        $model = GoodsCategory::findOne(['id'=>$id]);
        if(!$model->isLeaf()){
            \Yii::$app->session->setFlash('success','删除失败,该商品分类下有子分类');
        }else{
            $model->deleteWithChildren();
            \Yii::$app->session->setFlash('success','删除成功');
        }
        return $this->redirect(['goods-category/index']);
    }
}
