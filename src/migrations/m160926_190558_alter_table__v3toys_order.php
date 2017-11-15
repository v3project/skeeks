<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */

use yii\db\Schema;
use yii\db\Migration;

class m160926_190558_alter_table__v3toys_order extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%v3toys_order}}', 'geoobject');
        $this->addColumn('{{%v3toys_order}}', 'dadata_address', $this->text());
    }

    public function safeDown()
    {
        return true;
    }
}