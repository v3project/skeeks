<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\console\controllers;

use skeeks\cms\shop\models\ShopCmsContentElement;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Агенты v3toys
 *
 * Class AgentsController
 * @package v3toys\skeeks\console\controllers
 */
class AgentsController extends Controller
{
    /**
     * Обновление цен и налчия товаров
     */
    public function actionProductsUpdate()
    {
        //TODO:: реализовать

        $contentIds = (array) \Yii::$app->v3toysSettings->content_ids;
        if (!$contentIds)
        {
            $this->stdout("Не настроен v3toys комонент: {$total}\n", Console::FG_RED);
            return;
        }

        $elements = ShopCmsContentElement::find()->where(['content_id' => $contentIds])->all();
        if ($elements)
        {
            $total = count($elements);
            $this->stdout("Всего товаров: {$total}\n", Console::BOLD);

            /**
             * @var $element ShopCmsContentElement
             */
            foreach ($elements as $element)
            {
                $this->stdout("\t{$element->id}: {$element->name}\n");
                $v3id = $element->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName);
                if ($v3id)
                {
                    $response = \Yii::$app->v3toysApi->getProductsDataByIds(['products_ids' => $v3id]);
                    if ($response->isOk)
                    {
                        if ($response->data)
                        {
                            $data = $response->data[0];

                            $element->shopProduct->purchasing_price = ArrayHelper::getValue($data, 'buy_price');
                            $element->shopProduct->purchasing_currency = "RUB";

                            $element->shopProduct->baseProductPriceValue = ArrayHelper::getValue($data, 'price');
                            $element->shopProduct->baseProductPriceCurrency = "RUB";
                            $element->shopProduct->quantity = ArrayHelper::getValue($data, 'quantity');

                            if ($element->shopProduct->save())
                            {
                                $this->stdout("\tЦена={$element->shopProduct->baseProductPriceValue}; Количество={$element->shopProduct->quantity}\n", Console::FG_GREEN);
                            } else
                            {
                                $this->stdout("\tЦена и количество не обновлено\n", Console::FG_RED);
                            }
                        }
                    }
                } else
                {
                    $this->stdout("\t{$element->id}: {$element->name}\n", Console::FG_RED);
                    continue;
                }
            }
        }
    }

    /**
     * Обновление данных по заказам
     */
    public function actionOrdersUpdate()
    {
        //TODO:: реализовать
    }

    /**
     * Обновление данных по заявкам
     */
    public function actionMessagesUpdate()
    {
        //TODO:: реализовать
    }

    /**
     * Отправка новых заказов в v3toys
     */
    public function actionSubmitNewOrders()
    {
        //TODO:: реализовать
    }

    /**
     * Отправка новых заявок в v3toys
     */
    public function actionSubmitNewMessages()
    {
        //TODO:: реализовать
    }
}
