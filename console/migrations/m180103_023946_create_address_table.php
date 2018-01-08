<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m180103_023946_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer()->comment('用户id'),
            'name'=>$this->string(50)->comment('收货人名称'),
            'tel'=>$this->string(11)->comment('电话'),
            'province'=>$this->string(10)->comment('地址所属省'),
            'city'=>$this->string(10)->comment('地址所属市'),
            'area'=>$this->string(20)->comment('地址所属县'),
            'address'=>$this->string(100)->comment('详细地址'),
            'status'=>$this->integer(2)->comment('状态 0 删除 1有效'),
            'is_default'=>$this->integer(2)->comment('默认地址 0 否 1是'),
            'create_time'=>$this->integer()->comment('创建时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
