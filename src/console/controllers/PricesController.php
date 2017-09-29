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
class PricesController extends Controller
{
    /**
     * Обновление цен и налчия товаров
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

        $count = V3toysProductContentElement::find()->where(['content_id' => $contentIds])->count();
        $this->stdout("Всего товаров: {$count}\n", Console::BOLD);

        if ($count)
        {
            $i      = 0;
            $page   = 0;
            $step   = 1000;

            $pages = round($count/$step);
            if ($pages == 0)
            {
                $pages = 1;
            }

            $this->stdout("Всего страниц: {$pages}\n");
            sleep(1);

            for($i >= 0; $i <= $count; $i ++)
            {
                if ($i % $step == 0)
                {

                    $this->stdout("\tСтраница: {$page}\n");

                    $elements = V3toysProductContentElement::find()
                        ->where(['content_id' => $contentIds])
                        ->orderBy(['id' => SORT_ASC])
                        ->with('v3toysProductProperty')
                        ->limit($step)
                        ->offset($step * $page);

                    if ($elementsUpdate = $elements->all())
                    {
                        $this->stdout('found: ' . count($elementsUpdate));
                        $this->_updateElements($elementsUpdate);
                    } else
                    {
                        $this->stdout('not found');
                    }

                    $page = $page + 1;

                    //print_r(count($elements->all()));
                    //print_r($elements->createCommand()->rawSql);
                    //print_r($page);
                    //echo "\n";
                }

            }
        }
    }

    /**
     * @param V3toysProductContentElement[] $elements
     */
    protected function _updateElements($elements)
    {
        if ($elements)
        {
            $count = count($elements);
            $this->stdout("\t\tНайдено товаров: {$count}\n");

            $v3toysIds = [];
            foreach ($elements as $element)
            {
                if ($element->v3toysProductProperty) {
                    $v3toysIds[] = $element->v3toysProductProperty->v3toys_id;
                }
            }

            if (!$v3toysIds) {
                $this->stdout("\t\tНе надйено v3project id товаров для обновления\n");
            }

            $query = (new \yii\db\Query())
                        ->from('apiv5.product')
                        ->indexBy('id')
                        ->andWhere(['id' => $v3toysIds]);

            $prices = $query->all(\Yii::$app->dbV3project);

            $count = count($prices);
            $this->stdout("\t\tНайдено цен: {$count}\n");

            foreach ($elements as $element)
            {

                $v3id = $element->v3toysProductProperty->v3toys_id;
                $this->stdout("\t\t{$v3id}: {$element->name}\n", Console::BOLD);

                if ($v3id)
                {
                    if (!$dataPRice = ArrayHelper::getValue($prices, $v3id))
                    {
                        $this->stdout("\t\tНет данных по цене\n", Console::FG_RED);
                        continue;
                    }



                    $data = $dataPRice;
                    $priceFromApi = (float) ArrayHelper::getValue($data, 'guiding_realize_price');
                    $quantityFromApi = (int) ArrayHelper::getValue($data, 'guiding_available_quantity');

                    $isChange = false;

                    if (!$element->shopProduct)
                    {
                        $shopProduct = new ShopProduct([
                            'id' => $element->id
                        ]);

                        if ($shopProduct->save())
                        {
                            $this->stdout("\t\t\tShopProduct created\n");
                        } else
                        {
                            $this->stdout("\t\t\tShopProduct not created\n", Console::FG_RED);
                        }

                        $element->refresh();
                    }


                    $ourPrice = $priceFromApi + ($priceFromApi / 100 * \Yii::$app->v3toysSettings->price_discount_percent);
                    $ourPrice = round($ourPrice);
                    $discountValue = \Yii::$app->v3toysSettings->price_discount_percent;

                    $guiding_buy_price = (float) ArrayHelper::getValue($data, 'guiding_buy_price');
                    $mr_price = (float) ArrayHelper::getValue($data, 'mr_price');

                    if ($ourPrice > $guiding_buy_price)
                    {
                        $this->stdout("\t\t{$priceFromApi} + {$discountValue}% = {$ourPrice}\n");
                    } else
                    {
                        $ourPrice = $priceFromApi;
                        $this->stdout("\t\tНаша цена со скидкой {$ourPrice} < закупочной {$guiding_buy_price} оставим {$priceFromApi}\n");
                    }

                    if ($ourPrice < $mr_price)
                    {
                        $ourPrice = $mr_price;
                        $this->stdout("\t\t MR PRICE = {$mr_price}\n", Console::FG_YELLOW);
                    }


                    if ($ourPrice != $element->shopProduct->baseProductPriceValue)
                    {
                        $isChange = true;

                        $this->stdout("\t\tЦена изменилась была {$element->shopProduct->baseProductPriceValue} стала {$ourPrice}\n", Console::FG_GREEN);
                        $element->shopProduct->purchasing_price = round(ArrayHelper::getValue($data, 'guiding_buy_price'));
                        $element->shopProduct->purchasing_currency = "RUB";

                        $element->shopProduct->baseProductPriceValue = $ourPrice;
                        $element->shopProduct->baseProductPriceCurrency = "RUB";
                    } else
                    {
                        $this->stdout("\t\tЦена не менялась\n");
                    }

                    if ((int) ArrayHelper::getValue($data, 'guiding_available_quantity') != (int) $element->shopProduct->quantity)
                    {
                        $isChange = true;
                        $this->stdout("\t\tИзменилось количество {$element->shopProduct->quantity} стало {$quantityFromApi}\n", Console::FG_GREEN);
                        $element->shopProduct->quantity = (int) ArrayHelper::getValue($data, 'guiding_available_quantity');
                    } else
                    {
                        $this->stdout("\t\tКоличество не изменилось\n");
                    }


                    if ($isChange)
                    {
                        if ($element->shopProduct->save())
                        {
                            $this->stdout("\tДанные сохранены\n", Console::FG_GREEN);
                        } else
                        {
                            $error = Json::encode($element->shopProduct->errors);
                            $this->stdout("\tДанные не сохранены {$error}\n", Console::FG_RED);
                        }
                    }



                } else
                {
                    $this->stdout("\t\tНет v3id\n", Console::FG_RED);
                }
            }
        }
    }
}
