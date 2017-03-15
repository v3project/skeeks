<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\console\controllers;

use skeeks\cms\relatedProperties\propertyTypes\PropertyTypeText;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopPersonTypeProperty;
use skeeks\cms\shop\models\ShopProduct;
use skeeks\cms\shop\models\ShopProductPrice;
use v3toys\skeeks\models\V3toysProductContentElement;
use v3toys\skeeks\models\V3toysOrder;
use v3toys\skeeks\models\V3toysOrderStatus;
use v3toys\skeeks\models\V3toysProductProperty;
use v3toys\skeeks\models\V3toysShippingCity;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\httpclient\Client;

/**
 * Class ProductsController
 * @package v3toys\skeeks\console\controllers
 */
class ProductsController extends Controller
{
    /**
     * Обновление цен и налчия товаров
     */
    public function actionLoad()
    {
        ini_set("memory_limit","8192M");
        set_time_limit(0);


        $query = (new \yii\db\Query())
                    ->from('apiv5.affproduct');

        //$prices = $query->all(\Yii::$app->dbV3project);

        //Проверка настройки компонента
        $contentIds = (array) \Yii::$app->v3toysSettings->content_ids;
        if (!$contentIds)
        {
            $this->stdout("Не настроен v3toys комонент: {$total}\n", Console::FG_RED);
            return;
        }

        $count = $query->count(\Yii::$app->dbV3project);
        $this->stdout("Всего товаров базе 3project для вашего аффилиата: {$count}\n", Console::BOLD);


        foreach ($query->each(100, \Yii::$app->dbV3project) as $row)
        {
            //Данные по каждому товару аффилиата
            $v3id = ArrayHelper::getValue($row, 'product_id');
            if (!$v3id)
            {
                $this->stdout("\t\t Not found v3id\n", Console::FG_RED);
                continue;
            }

            $this->stdout("\t\t v3p_product_id: {$v3id}\n");

            if (V3toysProductContentElement::find()->joinWith('v3toysProductProperty as p')->andWhere(['p.v3toys_id' => $v3id])->exists())
            {
                $this->stdout("\t\t exist: {$v3id}\n", Console::FG_YELLOW);
                continue;
            }

            $property                       = new V3toysProductProperty();
            $property->consoleController    = $this;
            $property->v3toys_id            = ArrayHelper::getValue($row, 'product_id');

            $element = new V3toysProductContentElement();
            $element->name          = $title;
            $element->content_id    = 2;
            $element->meta_keywords = ArrayHelper::getValue($row, 'keywords');

            if ($element->save())
            {
                $this->stdout("\t\t Element added\n", Console::FG_GREEN);


                $property->id = $element->id;

                $property->sku = ArrayHelper::getValue($row, 'sku');

                if ($property->save())
                {
                    $this->stdout("\t\t Property added\n", Console::FG_GREEN);
                } else
                {
                    $this->stdout("\t\t Property not added\n", Console::FG_RED);
                    print_r($property->errors);
                    if ($element->delete())
                    {
                        $this->stdout("\t\t Element deleted\n");
                    }
                    die;
                }

            } else
            {
                $this->stdout("\t\t Element not added\n", Console::FG_RED);
                print_r($element->errors);
                die;
            }



        }

    }

}
