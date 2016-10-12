<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\console\controllers;

use skeeks\cms\helpers\StringHelper;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\CmsContentElementProperty;
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
use yii\helpers\Json;

/**
 * Агенты v3toys
 *
 * Class AgentsController
 * @package v3toys\skeeks\console\controllers
 */
class AgentsController extends Controller
{
    public function init()
    {
        parent::init();

        ini_set("memory_limit","8192M");
        set_time_limit(0);
    }

    /**
     * Обновление цен и налчия товаров
     */
    public function actionProductsUpdate()
    {
        ini_set("memory_limit","8192M");
        set_time_limit(0);

        $contentIds = (array) \Yii::$app->v3toysSettings->content_ids;
        if (!$contentIds)
        {
            $this->stdout("Не настроен v3toys комонент: {$total}\n", Console::FG_RED);
            return;
        }

        $count = ShopCmsContentElement::find()->where(['content_id' => $contentIds])->count();
        $this->stdout("Всего товаров: {$count}\n", Console::BOLD);

        if ($count)
        {
            /**
                 * @var $element CmsContentElement
             *
             **/

            /*$v3ids = [];
            foreach (ShopCmsContentElement::find()->where(['content_id' => $contentIds])->each(100) as $element) {
                /**
                 * @var $element CmsContentElement
                //$v3id = $element->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName);
                $v3id = $element->getRelatedElementProperties();
                echo $element->id . "\n";
                if ($element->id)
                {
                    $v3ids[] = $element->id;
                }
            }

            /*print_r($v3ids);die;*/

            foreach (ShopCmsContentElement::find()->where(['content_id' => $contentIds])->each(10) as $element) {

                $this->stdout("\t{$element->id}: {$element->name}\n");

                $v3id = $element->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName);
                if ($v3id)
                {
                    $response = \Yii::$app->v3toysApi->getProductsDataByIds(['products_ids' => $v3id]);

                    $this->stdout("\t\tОтвет получен из api\n");
                    if ($response->isOk)
                    {
                        if ($response->data)
                        {
                            $data = $response->data[0];
                            $priceFromApi = (float) ArrayHelper::getValue($data, 'price');
                            $quantityFromApi = (int) ArrayHelper::getValue($data, 'quantity');

                            $isChange = false;

                            if ($priceFromApi != $element->shopProduct->baseProductPriceValue)
                            {
                                $isChange = true;

                                $this->stdout("\t\tЦена изменилась была {$element->shopProduct->baseProductPriceValue} стала {$priceFromApi}\n", Console::FG_GREEN);
                                $element->shopProduct->purchasing_price = ArrayHelper::getValue($data, 'buy_price');
                                $element->shopProduct->purchasing_currency = "RUB";

                                $element->shopProduct->baseProductPriceValue = ArrayHelper::getValue($data, 'price');
                                $element->shopProduct->baseProductPriceCurrency = "RUB";
                            } else
                            {
                                $this->stdout("\t\tЦена не менялась\n");
                            }

                            if ((int) ArrayHelper::getValue($data, 'quantity') != (int) $element->shopProduct->quantity)
                            {
                                $isChange = true;
                                $this->stdout("\t\tИзменилось количество {$element->shopProduct->quantity} стало {$quantityFromApi}\n", Console::FG_GREEN);
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
                        }
                    } else
                    {
                        $this->stdout("\tApi response bad\n", Console::FG_RED);
                        continue;
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
