<?php
//$model -> status = 0; //默认选中
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'label')->textInput();
echo $form->field($model,'parent_id')->dropDownList($menu);
echo $form->field($model,'url')->textInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
 \yii\bootstrap\ActiveForm::end();