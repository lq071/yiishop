
<table class="table" id="table_id_example" class="display">
    <thead>
        <tr>
            <th>名称</th>
            <th>描述</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($rows as $row): ?>
        <tr data-id="<?=$row->name?>">
            <td><?=$row->name?></td>
            <td><?=$row->description?></td>
            <td><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['rbac/edit-role','name'=>$row->name])?>">修改</a>
                <a class="btn btn-danger" >删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['rbac/delete-role']).'?name=';
$str = <<<JS
//表格样式
            $('#table_id_example').DataTable({   language: {
        "sProcessing": "处理中...",
        "sLengthMenu": "显示 _MENU_ 项结果",
        "sZeroRecords": "没有匹配结果",
        "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
        "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
        "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
        "sInfoPostFix": "",
        "sSearch": "搜索:",
        "sUrl": "",
        "sEmptyTable": "表中数据为空",
        "sLoadingRecords": "载入中...",
        "sInfoThousands": ",",
        "oPaginate": {
            "sFirst": "首页",
            "sPrevious": "上页",
            "sNext": "下页",
            "sLast": "末页"
        },
        "oAria": {
            "sSortAscending": ": 以升序排列此列",
            "sSortDescending": ": 以降序排列此列"
        }
    }});
//删除
       $('.table').on('click','.btn-danger',function(data){
           var tr = $(this).closest('tr');
           if(confirm('是否确定删除该记录？')){
            var name = tr.attr('data-id');
            $.getJSON("$url"+name ,function(){
                tr.fadeOut();
            });
           }        
            });
JS;

$this->registerJs($str);
//表格样式插件文件
$this->registerCssFile('@web/dataTables/media/css/jquery.dataTables.css');
$this->registerJsFile('@web/dataTables/media/js/jquery.dataTables.js',[    'depends'=>\yii\web\JqueryAsset::className()
]);