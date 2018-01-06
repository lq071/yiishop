<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
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
                //父分类
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($model->parent_id){//如果查询到该分类有父分类 就追加到父分类下
                    $model->appendTo($parent);
                }else{//否则该分类就是根节点
                    $model->makeRoot();
                }
                // redis;
                $redis = new \Redis();
                $redis->open('127.0.0.1');
                $redis->del('category_view');

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
        //修改前原来 的 parent_id
       // $parent_id = $model->parent_id;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($model->parent_id){
                    $model->appendTo($parent);
                }else{
                    //解决 根节点修改为 根节点
                    if($model->getOldAttribute('parent_id')){
                        $model->makeRoot();
                    }else{
                        $model->save();
                    }
                }
                // redis;
                $redis = new \Redis();
                $redis->open('127.0.0.1');
                $redis->del('category_view');

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
    //权限
/*    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ],
        ];
    }*/
}
