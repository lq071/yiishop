<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'logo')->hiddenInput();
//=========================上传图片插件====================
echo <<<HTML
<div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
</div>
<img id="img" src=" $model->logo" width="150px" />
HTML;

$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js',[    'depends'=>\yii\web\JqueryAsset::className()
]);
$url = \yii\helpers\Url::to(['brand/upload']);
$js = <<<JS
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    swf:'/js/Uploader.swf',

    // 文件接收服务端。
    server: '{$url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/*'
    }
});

uploader.on( 'uploadSuccess', function( file,response ) {
    //$( '#'+file.id ).addClass('upload-state-done');
        $('#img').attr('src',response.url);
        $('#goods-logo').val(response.url)
       // console.debug(response)
});
JS;
$this->registerJs($js);
//==============================================
echo $form->field($model,'goods_category_id')->hiddenInput();
//============================使用 ztree 插件展示商品分类==========================
echo <<<HTML
<div>
   <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;

//引入
// $this->registerCssFile('@web/zTree/css/demo.css');
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.all.js',['depends'=>\yii\web\JqueryAsset::className()]);
$nodes = \backend\models\GoodsCategory::getNodes();
$nodesId = $model->goods_category_id;
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
		    $('#goods-goods_category_id').val(treeNode.id)
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

echo $form->field($model,'brand_id')->dropDownList(\yii\helpers\ArrayHelper::map($brand,'id','name'));
echo $form->field($model,'market_price')->textInput(['type'=>'number']);
echo $form->field($model,'shop_price')->textInput(['type'=>'number']);
echo $form->field($model,'stock')->textInput(['type'=>'number']);
echo $form->field($model,'is_on_sale')->radioList(['下架','在售']);
//echo $form->field($model,'status')->radioList(['回收站','正常']);
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo $form->field($model2,'content')->widget(\common\widgets\ueditor\Ueditor::className());
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
 \yii\bootstrap\ActiveForm::end();




