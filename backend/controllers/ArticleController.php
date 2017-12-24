<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
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
        $category =ArticleCategory::find()->all();
        if ($request->isPost){
            $model->load($request->post());
            $model2->load($request->post());
            if ($model->validate()) {
                $model->create_time = time();
                $model->save(false);
                $model2->article_id = $model->id;
                $model2 ->save(false);
                return $this->redirect(['article/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model,'model2'=>$model2,'category'=>$category]);
    }
    //修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $model =Article::findOne(['id'=>$id]);
        $model2 =ArticleDetail::findOne(['article_id'=>$id]);
        $category =ArticleCategory::find()->all();
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
        return $this->render('add', ['model' => $model,'model2'=>$model2,'category'=>$category]);
    }
    //删除
    public function actionDelete($id){
        $model =Article::findOne(['id'=>$id]);
        $model ->status = -1;
        $model->save();
        echo json_encode(1);
    }

//富文本编辑器
    public function actions()
    {
        return [
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }
}
