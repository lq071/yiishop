
<a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['article-category/add'])?>">添加</a>
<table class="table">
    <tr>
        <th>名称</th>
        <th>简介</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($rows as $row): ?>
    <tr data-id="<?=$row->id?>">
        <td><?=$row->name?></td>
        <td><?=$row->intro?></td>
        <td><?=$row->status == 0   ? '隐藏':($row->status == 1 ? '正常':'')?></td>
        <!--<td><?/*=$row->sort*/?></td>-->
        <td><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['article-category/edit','id'=>$row->id])?>">修改</a>
            <a class="btn btn-danger" >删除</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['article-category/delete']).'?id=';
$str = <<<JS
       $('.table').on('click','.btn-danger',function(data){
           var tr = $(this).closest('tr');
           if(confirm('是否确定删除该记录？')){
            var id = tr.attr('data-id');
            $.getJSON("$url"+id ,function(){
                tr.fadeOut();
            });
           }        
            });
JS;

$this->registerJs($str);
