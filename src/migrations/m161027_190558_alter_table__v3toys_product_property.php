<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */

use yii\db\Schema;
use yii\db\Migration;

class m161027_190558_alter_table__v3toys_product_property extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('v3toys_product_property', 'v3toys_id', $this->integer()->unique()->notNull());
    }

    public function safeDown()
    {}
}