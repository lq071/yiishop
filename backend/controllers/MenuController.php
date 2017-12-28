<?php

namespace backend\controllers;

use backend\models\Menu;
use yii\helpers\ArrayHelper;

class MenuController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        $rows = Menu::find()->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //添加
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new Menu();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['menu/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        $rows = Menu::find()->where(['parent_id'=>0])->asArray()->all();
        $menu= ArrayHelper::map($rows,'id','label');
        //$menu =array_merge([0=>'顶级菜单'],$menu);
       $menu[0]='顶级菜单';
        return $this->render('add',['model'=>$model,'menu'=>$menu]);
    }
    //添加
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $model = Menu::findOne(['id'=>$id]);
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);
                \Yii::$app->session->setFlash('success','更新成功');
                return $this->redirect(['menu/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        $rows = Menu::find()->where(['parent_id'=>0])->asArray()->all();
        $menu= ArrayHelper::map($rows,'id','label');
        //$menu =array_merge([0=>'顶级菜单'],$menu);
        $menu[0]='顶级菜单';
        return $this->render('add',['model'=>$model,'menu'=>$menu]);
    }
    //删除
    public function actionDelete($id){
        $model = Menu::findOne(['id'=>$id]);
        if($model->parent_id==0){
            \Yii::$app->session->setFlash('success','该菜单下有子菜单');
            return $this->redirect(['menu/index']);
        }else{
            $model->delete();
            echo json_encode(1);
        }
    }
}
