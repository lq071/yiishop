<!-- 头部上半部分 start 包括 logo、搜索、用户中心和购物车结算 -->
<div class="logo w1210">
    <h1 class="fl"><a href="index.html"><img src="/images/logo.png" alt="京西商城" width="259" height="50"></a></h1>
    <!-- 头部搜索 start -->
    <div class="search fl">
        <div class="search_form">
            <div class="form_left fl"></div>
            <form action="<?=\yii\helpers\Url::to(['site/search'])?>" name="search" method="get" class="fl">
                <input type="text" class="txt" name="keywords" value="请输入商品关键字" /><input type="submit" class="btn" value="搜索" />
            </form>
            <div class="form_right fl"></div>
        </div>

        <div style="clear:both;"></div>

        <div class="hot_search">
            <strong>热门搜索:</strong>
            <a href="">D-Link无线路由</a>
            <a href="">休闲男鞋</a>
            <a href="">TCL空调</a>
            <a href="">耐克篮球鞋</a>
        </div>
    </div>
<div class="user fl">
    <dl>
        <dt>
            <em></em>
            <a href="">用户中心</a>
            <b></b>
        </dt>
        <dd>
            <div class="prompt">
                <?php if(Yii::$app->user->isGuest){?>
                您好，请<a href="<?=\yii\helpers\Url::to(['login/login'])?>">登录</a>
                <?php }else{?>
                    <b>您好,<?=Yii::$app->user->identity->username?></b>
                <?php }?>
            </div>
            <div class="uclist mt10">
                <ul class="list1 fl">
                    <li><a href="">用户信息></a></li>
                    <li><a href="<?=\yii\helpers\Url::to(['order/detail'])?>">我的订单></a></li>
                    <li><a href="<?=\yii\helpers\Url::to(['address/index'])?>">收货地址></a></li>
                    <li><a href="">我的收藏></a></li>
                </ul>

                <ul class="fl">
                    <li><a href="">我的留言></a></li>
                    <li><a href="">我的红包></a></li>
                    <li><a href="">我的评论></a></li>
                    <li><a href="">资金管理></a></li>
                </ul>

            </div>
            <div style="clear:both;"></div>
            <div class="viewlist mt10">
                <h3>最近浏览的商品：</h3>
                <ul>
                    <li><a href=""><img src="/images/view_list1.jpg" alt="" /></a></li>
                    <li><a href=""><img src="/images/view_list2.jpg" alt="" /></a></li>
                    <li><a href=""><img src="/images/view_list3.jpg" alt="" /></a></li>
                </ul>
            </div>
        </dd>
    </dl>
</div>
<div class="cart fl">
    <dl>
        <dt>
            <a href="<?=\yii\helpers\Url::to(['site/cart'])?>">去购物车结算</a>
            <b></b>
        </dt>
    <!--    <dd>
            <div class="prompt">
                购物车中还没有商品，赶紧选购吧！
            </div>
        </dd>-->
    </dl>
</div>