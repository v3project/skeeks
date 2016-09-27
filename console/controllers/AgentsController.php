<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\console\controllers;

use skeeks\cms\helpers\StringHelper;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopOrder;
use skeeks\cms\shop\models\ShopOrderStatus;
use v3toys\skeeks\helpers\ShopOrderHelper;
use v3toys\skeeks\models\V3toysMessage;
use v3toys\skeeks\models\V3toysOrder;
use v3toys\skeeks\V3toysModule;
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
     * @param int $countDay за последние количество дней
     */
    public function actionOrdersUpdate($countDay = 3)
    {
        if ($orders = V3toysOrder::find()->where(['>=', 'created_at', time() - 3600*24*$countDay])->all())
        {
            $totalOrders = count($orders);
            $this->stdout("Заказов к обновлению: {$totalOrders}\n", Console::BOLD);
            /**
             * @var $order V3toysOrder
             */
            foreach ($orders as $order)
            {
                $response = \Yii::$app->v3toysApi->getOrderStatusById($order->id);

                if ($response->isOk)
                {
                    $newStatus = ArrayHelper::getValue($response->data, 'status_id');
                    if ((int) $newStatus != (int) $order->v3toys_status_id)
                    {
                        $order->v3toys_status_id = $newStatus;
                        if ($order->save())
                        {
                            $this->stdout("Заказ #{$order->id} новый статус : {$newStatus}\n", Console::FG_GREEN);
                        } else
                        {
                            $this->stdout("Заказ #{$order->id} не обновлен статус : {$newStatus}\n", Console::FG_RED);
                        }
                    }

                } else
                {
                    $this->stdout("Ошибка апи : {$response->error_message}\n", Console::FG_RED);
                }
            }
        }
    }

    /**
     * Обновление данных по заявкам
     */
    public function actionMessagesUpdate()
    {
        if ($orders = V3toysMessage::find()->where(['>=', 'created_at', time() - 3600*24])->all())
        {
            $totalOrders = count($orders);
            $this->stdout("Заявок к обновлению: {$totalOrders}\n", Console::BOLD);
            /**
             * @var $order V3toysMessage
             */
            foreach ($orders as $order)
            {
                $response = \Yii::$app->v3toysApi->getMessageStatus($order->id);

                if ($response->isOk)
                {
                    $status = ArrayHelper::getValue($response->data, 'status');
                    if ((string) $status != (string) $order->status_name)
                    {
                        $order->status_name = $status;
                        if ($order->save())
                        {
                            $this->stdout("Заявка #{$order->id} новый статус : {$status}\n", Console::FG_GREEN);
                        } else
                        {
                            $this->stdout("Заявка #{$order->id} не обновлен статус : {$status}\n", Console::FG_RED);
                        }
                    }

                } else
                {
                    $this->stdout("Ошибка апи : {$response->error_message}\n", Console::FG_RED);
                }
            }
        }
    }

    /**
     * Отправка новых заказов в v3toys
     */
    public function actionSubmitNewOrders()
    {
        if ($orders = V3toysOrder::find()->where(['v3toys_order_id' => null])->andWhere(['>=', 'created_at', time() - 3600*24])->all())
        {
            $totalOrders = count($orders);
            $this->stdout("Заказов к отправке в v3toys: {$totalOrders}\n", Console::BOLD);

            //Есть заказы к отрпавке
            /**
             * @var V3toysOrder $order
             */
            foreach ($orders as $order)
            {
                $response = \Yii::$app->v3toysApi->createOrder($order->getApiRequestData());

                if ($response->isError)
                {
                    $message = "Заказ #{$order->id} не отправлен в апи: {$response->error_code} {$response->error_message}";
                    \Yii::error($message, V3toysModule::className());
                    $this->stdout("\t$message\n", Console::FG_RED);
                }

                if ($response->isOk)
                {

                    $v3ToysOrderId = ArrayHelper::getValue((array) $response->data, 'order_id');
                    $order->v3toys_order_id = $v3ToysOrderId;
                    if ($order->save())
                    {
                        $this->stdout("Заказ отправлен в v3toys и получил #{$v3ToysOrderId}\n", Console::FG_GREEN);
                    } else
                    {
                        $message = "Заказ отправлен в v3toys и получил #{$v3ToysOrderId}, но не сохранен в нашей базе\n";
                        \Yii::warning($message, V3toysModule::className());
                        $this->stdout($message, Console::FG_YELLOW);
                    }

                }
            }
        } else
        {
            $this->stdout("Нет заказов к отправке в v3toys\n", Console::BOLD);
        }
    }

    /**
     * Отправка новых заявок в v3toys
     */
    public function actionSubmitNewMessages()
    {
        if ($orders = V3toysMessage::find()
            ->andWhere([
                'or',
                ['status_name' => ''],
                ['status_name' => null],
            ])
            ->andWhere(['>=', 'created_at', time() - 3600*24])
            ->all())
        {
            $totalOrders = count($orders);
            $this->stdout("Заявок к отправке в v3toys: {$totalOrders}\n", Console::BOLD);

            //Есть заказы к отрпавке
            /**
             * @var V3toysMessage $order
             */
            foreach ($orders as $order)
            {
                $response = \Yii::$app->v3toysApi->createMessage($order->getApiRequestData());

                if ($response->isError)
                {
                    $message = "Заявка #{$order->id} не отправлен в апи: {$response->error_code} {$response->error_message}";
                    \Yii::error($message, V3toysModule::className());
                    $this->stdout("\t$message\n", Console::FG_RED);
                }

                if ($response->isOk)
                {
                    $this->stdout("Заявка отправлена в v3toys\n", Console::FG_GREEN);
                }
            }
        } else
        {
            $this->stdout("Нет заказов к отправке в v3toys\n", Console::BOLD);
        }
    }
}
