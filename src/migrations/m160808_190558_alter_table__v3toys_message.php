<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */

use yii\db\Schema;
use yii\db\Migration;

class m160808_190558_alter_table__v3toys_message extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%v3toys_message}}', 'status_name', $this->string(255));
    }

    public function safeDown()
    {
        return true;
    }
}