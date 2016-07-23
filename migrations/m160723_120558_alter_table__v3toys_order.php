<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */
use yii\db\Schema;
use yii\db\Migration;

class m160723_120558_alter_table__v3toys_order extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%v3toys_order}}', 'v3toys_status_id', $this->integer());
        $this->addColumn('{{%v3toys_order}}', 'key', $this->string(32)->unique());
    }

    public function safeDown()
    {
        return true;
    }
}