<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */

use yii\db\Schema;
use yii\db\Migration;

class m160925_190558_alter_table__v3toys_order extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%v3toys_order}}', 'geoobject', $this->text());
    }

    public function safeDown()
    {
        return true;
    }
}