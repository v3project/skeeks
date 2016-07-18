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
            $this->stdout("Import products from yupe: {$total}\n", Console::BOLD);
        }

        //ShopCmsContentElement::find()->where(['content_id' => ])



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
