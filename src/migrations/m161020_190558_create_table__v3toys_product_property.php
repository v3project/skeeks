<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */

use yii\db\Schema;
use yii\db\Migration;

class m161020_190558_create_table__v3toys_product_property extends Migration
{
    public function safeUp()
    {
        $tableExist = $this->db->getTableSchema("{{%v3toys_product_property}}", true);
        if ($tableExist) {
            return true;
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable("{{%v3toys_product_property}}", [
            'id' => $this->primaryKey(),
            'v3toys_id' => $this->integer(),
            'hero_id' => $this->integer(),
            'series_id' => $this->integer(),
            'sex' => $this->integer(),
            'age_from' => $this->decimal(18, 2)->notNull()->defaultValue(0),
            'age_to' => $this->decimal(18, 2)->notNull()->defaultValue(0),
            'to_who' => $this->string(255),
            'model' => $this->string(255),
            'color' => $this->string(255),
            'scale' => $this->string(255), //
            'number_of_parts' => $this->string(255), //70 шт.
            'complect' => $this->string(255),
            'players_number' => $this->string(255),
            'allowable_weight' => $this->string(255),
            'availability_batteries' => $this->string(255),
            'batteries_type' => $this->string(255),
            'game_time' => $this->string(255),
            'charge_time' => $this->string(255),
            'range' => $this->string(255),
            'composition' => $this->string(255),
            'number_pages' => $this->string(255),
            'volume' => $this->string(255),
            'size_of_box' => $this->string(255),
            'size_of_toy' => $this->string(255),
            'producing_country' => $this->string(255),
            'packing' => $this->integer(),
            'extra' => $this->text(),
            'sku' => $this->string(255),
            'stock_barcode' => $this->string(255),
            'v3toys_brand_name' => $this->string(255),
            'v3toys_title' => $this->string(255),
            'v3toys_description' => $this->string(255),
            'v3toys_video' => $this->string(255),
        ], $tableOptions);

        $this->createIndex('v3toys_product_property__sex', '{{%v3toys_product_property}}', 'sex');
        $this->createIndex('v3toys_product_property__age_from', '{{%v3toys_product_property}}', 'age_from');
        $this->createIndex('v3toys_product_property__age_to', '{{%v3toys_product_property}}', 'age_to');

        $this->addForeignKey(
            'v3_p_p__content_element', "{{%v3toys_product_property}}",
            'id', '{{%cms_content_element}}', 'id', 'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey("v3toys_product_property__content_element", "{{%v3toys_product_property}}");
        $this->dropTable("{{%v3toys_product_property}}");
    }
}