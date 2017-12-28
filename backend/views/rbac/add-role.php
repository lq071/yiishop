<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'description');
echo $form->field($model,'permission')
    ->checkboxList(\yii\helpers\ArrayHelper::map($permission,'name','description'));
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();