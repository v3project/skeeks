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
 * Class InitV2Controller
 *
 * @package v3toys\skeeks\console\controllers
 */
class InitV2Controller extends Controller
{

    /**
     * @deprecated
     */
    public function actionProducts()
    {
        $contentIds = (array)\Yii::$app->v3toysSettings->content_ids;
        if (!$contentIds) {
            $this->stdout("Не настроен v3toys комонент: {$total}\n", Console::FG_RED);
            return;
        }

        $count = ShopCmsContentElement::find()->where(['content_id' => $contentIds])->count();
        $this->stdout("Всего товаров: {$count}\n", Console::BOLD);

        if ($count) {
            foreach (ShopCmsContentElement::find()->where(['content_id' => $contentIds])->each(10) as $element) {

                $this->stdout("\t{$element->id}: {$element->name}\n");

                $v3id = $element->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName);
                if ($v3id) {
                    if (V3toysProductProperty::find()->where(['v3toys_id' => $v3id])->one()) {
                        $this->stdout("\t\t Property exist\n", Console::FG_YELLOW);
                        continue;
                    }

                    $property = new V3toysProductProperty();
                    $property->v3toys_id = $v3id;
                    $property->id = $element->id;
                    if ($property->save()) {
                        $this->stdout("\t\t Property added\n", Console::FG_GREEN);
                    } else {
                        $this->stdout("\t\t Property not added\n", Console::FG_RED);
                        print_r($property->errors);
                        die;
                    }
                }
            }
        }
    }

    public function actionInfo()
    {
        $query = (new \yii\db\Query())
            ->from('apiv5.affiliate')
            ->indexBy('id');

        $prices = $query->all(\Yii::$app->dbV3project);

        print_r($prices);
        die;
    }

    /**
     * Добавление товаров в базу v3project разовое
     * Товар будет создан в базе v3project если его еще тм нет, со статусом ПРОВЕРЕН!
     *
     * @param int $needText нужно писать текст или нет
     */
    public function actionOnceToV3project($needText = 0)
    {
        ini_set("memory_limit", "8192M");
        set_time_limit(0);

        $this->stdout("Start import product from content to v3\n");

        $url = 'http://back.v3project.ru/index.php?r=contents/api/v1/products/addproduct';
        //$url = 'http://back.v3project.ru.vps108.s2.h.skeeks.com/index.php?r=contents/api/v1/products/addproduct';

        $contentIds = (array)\Yii::$app->v3toysSettings->content_ids;
        if (!$contentIds) {
            $this->stdout("Не настроен v3toys комонент: {$total}\n", Console::FG_RED);
            return;
        }

        $count = ShopCmsContentElement::find()->where(['content_id' => $contentIds])->count();
        $this->stdout("Products: $count\n");
        if ($count) {
            /**
             * @var V3toysProductContentElement $shopContentElement
             */
            foreach (V3toysProductContentElement::find()
                         ->where(['content_id' => $contentIds])
                         //->andWhere('id > 13007')
                         ->orderBy(['id' => SORT_ASC])
                         ->each(10) as $shopContentElement) {
                $this->stdout("{$shopContentElement->id}: {$shopContentElement->name}\n");

                $client = new Client([
                    'requestConfig' => [
                        'format' => Client::FORMAT_JSON
                    ]
                ]);

                $requestData = [
                    'product_id' => $shopContentElement->v3toysProductProperty->v3toys_id,
                    'title' => $shopContentElement->name,
                    'alt_title' => "",

                    'meta_title' => $shopContentElement->meta_title,
                    'meta_description' => $shopContentElement->meta_description,
                    'meta_keywords' => $shopContentElement->meta_keywords,

                    'description' => $shopContentElement->description_full,
                ];

                if ($shopContentElement->image) {
                    $requestData['main_image_path'] = $shopContentElement->image->absoluteSrc;
                }

                if ($shopContentElement->images) {
                    foreach ($shopContentElement->images as $image) {
                        $requestData['second_image_paths'][] = $image->absoluteSrc;
                    }
                }

                $request = [
                    'aff_key' => \Yii::$app->v3toysSettings->affiliate_key,
                    'data' => $requestData
                ];

                if ($needText == 1) {
                    $request['mode'] = 'need_text';
                }

                //print_r($request);

                $httpResponse = $client->createRequest()
                    ->setMethod("POST")
                    ->setUrl($url)
                    ->addHeaders(['Content-type' => 'application/json'])
                    ->addHeaders(['user-agent' => 'JSON-RPC PHP Client'])
                    ->setData($request)
                    ->setOptions([
                        'timeout' => 30
                    ])->send();;

                if ($httpResponse->isOk) {
                    $this->stdout("Ok\n");
                    if ($error = ArrayHelper::getValue((array)$httpResponse->data, 'error')) {
                        $this->stdout("Error: {$error}\n", Console::FG_RED);
                    } else {
                        $this->stdout("Success\n", Console::FG_GREEN);
                        print_r($httpResponse->data);
                    }
                } else {
                    $this->stdout("Error\n", Console::FG_RED);
                    print_r($httpResponse->content);
                }

            }
        }


        $this->stdout("End import product from content \n\n");
    }
}
