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
use v3toys\skeeks\models\V3toysProductContentElement;
use v3toys\skeeks\models\V3toysProductProperty;
use v3toys\skeeks\models\V3toysOrder;
use v3toys\skeeks\models\V3toysOrderStatus;
use v3toys\skeeks\models\V3toysShippingCity;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\httpclient\Client;

/**
 * Class InitController
 *
 * @package v3toys\parsing\console\controllers
 */
class PropertiesController extends Controller
{


    /**
     * Загрузка дополнительных свойств товаров получение из базы 3toys
     */
    public function actionLoad()
    {
        ini_set("memory_limit","8192M");
        set_time_limit(0);

        $contentIds = (array) \Yii::$app->v3toysSettings->content_ids;
        if (!$contentIds)
        {
            $this->stdout("Не настроен v3toys комонент: {$total}\n", Console::FG_RED);
            return;
        }

        $total = V3toysProductContentElement::find()->where(['content_id' => $contentIds])->count();
        $this->stdout("Всего товаров в базе: {$total}\n", Console::BOLD);

        $query = V3toysProductContentElement::find()
            ->where(['content_id' => $contentIds])
            ->joinWith('v3toysProductProperty as v3p')
            ->andWhere([
                'sex' => null
            ])
        ;

        $total = $query->count();

        $this->stdout("Товаров без свойств: {$total}\n", Console::BOLD);

        sleep(5);

        $query = V3toysProductContentElement::find()
            ->where(['content_id' => $contentIds])
            ->joinWith('v3toysProductProperty as v3p')
            ->andWhere([
                'sex' => null
            ])
        ;

        if ($total)
        {
            /**
             * @var $element V3toysProductContentElement
             */
            foreach ($query->orderBy(['id' => SORT_DESC])->each(100) as $element)
            {
                $this->stdout("\t{$element->id}: {$element->name}\n");
                //$v3id = $element->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName);
                $v3id = $element->v3toysProductProperty->v3toys_id;
                if ($v3id)
                {
                    if (!$v3toysProperty = $element->v3toysProductProperty)
                    {
                        $v3toysProperty = new V3toysProductProperty([
                            'id' => $element->id
                        ]);

                        if (!$v3toysProperty->save())
                        {
                            $this->stdout("\t\t\t Свойство не создано\n", Console::FG_RED);
                            continue;
                        } else
                        {
                            $this->stdout("\t\t\t Свойство создано\n", Console::FG_GREEN);
                        }
                    }

                    $query = (new \yii\db\Query())
                        ->andWhere(['pid' => $v3id])
                        ->from('apiv5.to_del_oldkw_product_extender')
                        ->limit(1);

                    $query2 = (new \yii\db\Query())
                        ->from('apiv5.product')
                        ->andWhere(['id' => $v3id])
                        ->limit(1);

                    $dataProperties = $query->one(\Yii::$app->dbV3project);
                    $dataAdditional = $query2->one(\Yii::$app->dbV3project);

                    if ($dataProperties)
                    {
                        $v3toysProperty->hero_id = ArrayHelper::getValue($dataProperties, 'hero_id');
                        $v3toysProperty->series_id = ArrayHelper::getValue($dataProperties, 'series_id');
                        $v3toysProperty->sex = ArrayHelper::getValue($dataProperties, 'sex');
                        $v3toysProperty->age_from = ArrayHelper::getValue($dataProperties, 'age_from');
                        $v3toysProperty->age_to = ArrayHelper::getValue($dataProperties, 'age_to');
                        $v3toysProperty->to_who = ArrayHelper::getValue($dataProperties, 'to_who');
                        $v3toysProperty->model = ArrayHelper::getValue($dataProperties, 'model');
                        $v3toysProperty->color = ArrayHelper::getValue($dataProperties, 'color');
                        $v3toysProperty->scale = ArrayHelper::getValue($dataProperties, 'scale');
                        $v3toysProperty->number_of_parts = ArrayHelper::getValue($dataProperties, 'number_of_parts');
                        $v3toysProperty->complect = ArrayHelper::getValue($dataProperties, 'complect');
                        $v3toysProperty->players_number = ArrayHelper::getValue($dataProperties, 'players_number');
                        $v3toysProperty->allowable_weight = ArrayHelper::getValue($dataProperties, 'allowable_weight');
                        $v3toysProperty->availability_batteries = ArrayHelper::getValue($dataProperties, 'availability_batteries');
                        $v3toysProperty->batteries_type = ArrayHelper::getValue($dataProperties, 'batteries_type');
                        $v3toysProperty->game_time = ArrayHelper::getValue($dataProperties, 'game_time');
                        $v3toysProperty->charge_time = ArrayHelper::getValue($dataProperties, 'charge_time');
                        $v3toysProperty->range = ArrayHelper::getValue($dataProperties, 'range');
                        $v3toysProperty->composition = ArrayHelper::getValue($dataProperties, 'composition');
                        $v3toysProperty->number_pages = ArrayHelper::getValue($dataProperties, 'number_pages');
                        $v3toysProperty->volume = ArrayHelper::getValue($dataProperties, 'volume');
                        $v3toysProperty->size_of_box = ArrayHelper::getValue($dataProperties, 'size_of_box');
                        $v3toysProperty->size_of_toy = ArrayHelper::getValue($dataProperties, 'size_of_toy');
                        $v3toysProperty->producing_country = ArrayHelper::getValue($dataProperties, 'producing_country');
                        $v3toysProperty->extra = ArrayHelper::getValue($dataProperties, 'extra');
                        $v3toysProperty->packing = ArrayHelper::getValue($dataProperties, 'packing');

                        $v3toysProperty->sku = ArrayHelper::getValue($dataAdditional, 'sku');
                        $barcode = ArrayHelper::getValue($dataAdditional, 'stock_barcodes');
                        if ($barcode)
                        {
                            $barcodeData = Json::decode($barcode);
                            if ($barcodeData && is_array($barcodeData))
                            {
                                $v3toysProperty->stock_barcode = $barcodeData[0];
                            }
                        }

                        if ($v3toysProperty->save())
                        {
                            $this->stdout("\t\t Данные сохранены\n", Console::FG_GREEN);
                        } else
                        {
                            $error = Json::encode($v3toysProperty->errors);
                            $this->stdout("\t\t Данные не сохранены: {$error}\n", Console::FG_RED);
                        }

                    } else
                    {
                        $this->stdout("\t\t Данные не получены со стороны v3toys\n", Console::FG_RED);
                    }
                } else
                {
                    $this->stdout("\t\t v3toysId не указан\n", Console::FG_RED);
                    continue;
                }
            }
        }
    }
}
