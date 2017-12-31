<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\ArticleCategory;


class ArticleCategoryController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        $rows = ArticleCategory::find()->where(['status'=>[0,1]])->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //添加
    public function actionAdd()
    {
        $request = \Yii::$app->request;
        $model = new ArticleCategory();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);
                return $this->redirect(['article-category/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $model =ArticleCategory::findOne(['id'=>$id]);
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);
                return $this->redirect(['article-category/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //删除
    public function actionDelete($id){
        $model =ArticleCategory::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        echo json_encode(1);
    }
    //权限
 /*   public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ],
        ];
    }*/

}
