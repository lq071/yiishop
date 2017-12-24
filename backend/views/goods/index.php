<form class="form-horizontal">
        <div class="col-xs-2">
            <input type="text" name="sn" class="form-control" placeholder="商品编号">
        </div>
        <div class="col-xs-2">
            <input type="text" name="name" class="form-control" placeholder="商品名称">
        </div>
        <div class="col-xs-2">
            <input type="number" name="market_price" class="form-control" placeholder="市场价格">
        </div>
        <div class="col-xs-2">
            <input type="number" name="shop_price" class="form-control" placeholder="商品价格">
        </div>

</form>
<button id="search" class="btn btn-default">搜索</button>
<table class="table">
    <thead>
    <tr><th>名称</th>
    <th>货号</th>
    <th>LOGO</th>
    <th>商品分类</th>
    <th>品牌</th>
    <th>市场价格</th>
    <th>商品价格</th>
    <th>库存</th>
    <th>是否在售</th>
    <th>状态</th>
    <th>排序</th>
    <th>浏览次数</th>
    <th>操作</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach($rows as $row): ?>
    <tr data-id="<?=$row->id?>">
        <td><?=$row->name?></td>
        <td><?=$row->sn?></td>
        <td><img src="<?=$row->logo?>" width="75px"></td>
        <td><?=$row->goodsCategory ? $row->goodsCategory->name : ''?></td>
        <td><?=$row->brand ? $row->brand->name : ''?></td>
        <td><?=$row->market_price?></td>
        <td><?=$row->shop_price?></td>
        <td><?=$row->stock?></td>
        <td><?=$row->is_on_sale ==1 ? '在售':'下架'?></td>
        <td><?=$row->status == 1 ? '正常':'回收站'?></td>
        <td><?=$row->sort?></td>
        <td><?=$row->view_times?></td>
        <td>
            <a class="btn btn-info" href="<?=\yii\helpers\Url::to(['goods-gallery/index','id'=>$row->id])?>">相册</a>
            <a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['goods/edit','id'=>$row->id])?>">修改</a>
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
$url = \yii\helpers\Url::to(['goods/delete']).'?id=';
$url_search = \yii\helpers\Url::to(['goods/search']);
$url_edit = \yii\helpers\Url::to(['goods/edit']);
$str = <<<JS
//搜索
$("#search").click(function(){
    $.ajax({
        url: "{$url_search}",
        data: $(".form-horizontal").serialize(),
        type: "post",
        dataType: 'json',
        success: function(data){
            var detail = data.details; //array
            $(".table tbody").html("");
            if(detail){
                 var html = "";
                $.each(detail,function(i,e){
                    //console.log(e);
                    html +='<tr data-id="'+e["id"]+'">';
                    html +='<td>'+e["name"]+'</td>';
                    html +='<td>'+e["sn"]+'</td>';
                    html +='<td><img src="'+e["logo"]+'" width="75px"></td>';
                    html +='<td>'+e["goods_category_id"]+'</td>';
                    html +='<td>'+e["brand_id"]+'</td>';
                    html +='<td>'+e["market_price"]+'</td>';
                    html +='<td>'+e["shop_price"]+'</td>';
                    html +='<td>'+e["stock"]+'</td>';
                    html +='<td>'+(e["is_on_sale"]==1?'在售':'下架')+'</td>';
                    html +='<td>'+(e["status"]==1?'正常':'回收站')+'</td>';
                    html +='<td>'+e["sort"]+'</td>';
                    console.log(e["view_times"] == null);
                    html +='<td>'+(e["view_times"] == null ?'':e["view_times"])+'</td>';
                    html +='<td><a class="btn btn-primary" href="{$url_edit}?id='+e["id"]+'">修改</a><a class="btn btn-danger" >删除</a></td></tr>';
                });
                $(".table tbody").html(html);
            }
            
        } 
    });
    
});

//删除
       $('.table').on('click','.btn-danger',function(data){
           var tr = $(this).closest('tr');
           if(confirm('是否确定删除该记录？')){
            var id = tr.attr('data-id');
            $.getJSON("$url"+id ,function(data){
                if(data==1){
                    tr.fadeOut();
                }else{
                    alert("删除失败");
                }
            });
           }        
            });
JS;

$this->registerJs($str);
