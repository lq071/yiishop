<?php

use yii\db\Migration;

class m171224_070951_alter_user_column extends Migration
{
    public function up()
    {
        $this->addColumn('user','last_login_time','integer comment"最后登录时间"');
        $this->addColumn('user','last_login_ip','string(30) comment"最后登录ip"');
    }

    public function down()
    {
       // echo "m171224_070951_alter_user_column cannot be reverted.\n";
        $this->dropColumn('user','last_login_time');
        $this->dropColumn('user','last_login_ip');
        //return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
