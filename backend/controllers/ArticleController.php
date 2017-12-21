<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleDetail;

class ArticleController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        $rows = Article::find()->where(['status'=>[0,1]])->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //添加
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new Article();
        $model2 = new ArticleDetail();
        if ($request->isPost){
            $model->load($request->post());
            $model2->load($request->post());
            if ($model->validate()) {

                $model->save(false);
                $model2->article_id = $model->id;
                $model2 ->save(false);

                return $this->redirect(['article/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model,'model2'=>$model2]);
    }
    //修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $model =Article::findOne(['id'=>$id]);
        $model2 =ArticleDetail::findOne(['article_id'=>$id]);
        if ($request->isPost){
            $model->load($request->post());
            $model2->load($request->post());
            if ($model->validate()) {

                $model->save(false);
                //$model2->article_id = $model->id;
                $model2 ->save(false);

                return $this->redirect(['article/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model,'model2'=>$model2]);
    }
    //删除
    public function actionDelete($id){
        $model =Article::findOne(['id'=>$id]);
        $model ->status = -1;
        $model->save();
        echo json_encode(1);
    }
}
