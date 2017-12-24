<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput(['placeholder'=>'请输入分类名']);
echo $form->field($model,'parent_id')->hiddenInput();
//============================使用 ztree 插件展示商品分类==========================
echo <<<HTML
<div>
   <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;
/**
 * @var $this \yii\web\View
 */
//引入
// $this->registerCssFile('@web/zTree/css/demo.css');
 $this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
 $this->registerJsFile('@web/zTree/js/jquery.ztree.all.js',['depends'=>\yii\web\JqueryAsset::className()]);

$nodes = \backend\models\GoodsCategory::getNodes();
$nodesId = $model->parent_id;
$js=<<<JS
  var zTreeObj;
   // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
   var setting = {
	data: {
		simpleData: {
			enable: true,
			idKey: "id",
			pIdKey: "parent_id",
			rootPId: 0
		}
	},
	callback: {
		onClick: function(event, treeId, treeNode){
		    $('#goodscategory-parent_id').val(treeNode.id)
		}
	}
};
   // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
   var zNodes = {$nodes};
      zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
      //展开节点
      zTreeObj.expandAll(true);
      //回显节点
      var node = zTreeObj.getNodeByParam("id", '{$nodesId}', null);
      zTreeObj.selectNode(node);
JS;
$this->registerJs($js);
//==============================================================================
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();