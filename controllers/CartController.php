<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\controllers;
use skeeks\cms\base\Controller;
use skeeks\cms\helpers\RequestResponse;
use v3toys\skeeks\models\V3toysOrder;
use v3toys\skeeks\V3toysModule;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Class CartController
 * @package v3toys\skeeks\controllers
 */
class CartController extends Controller
{

    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'validate'  => ['post'],
                ],
            ],
        ]);
    }


    public function actionIndex()
    {
        return $this->render($this->action->id);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFinish()
    {

        if (!$key = \Yii::$app->request->get('key'))
        {
            throw new NotFoundHttpException('Заказ не найден');
        }

        if (!$v3toysOrder = V3toysOrder::findOne(['key' => $key]))
        {
            throw new NotFoundHttpException("Заказ #{$key} не найден");
        }

        $this->view->title = "Заказ номер {$v3toysOrder->id} успешно оформлен";


        return $this->render($this->action->id, ['model' => $v3toysOrder]);
    }

    /**
     * @return string
     */
    public function actionCheckout()
    {
        $this->view->title = \Yii::t('skeeks/shop/app', 'Checkout').' | '.\Yii::t('skeeks/shop/app', 'Shop');

        $v3toysOrder = V3toysOrder::createCurrent();
        $v3toysOrder->loadDefaultValues();

        $rr = new RequestResponse();

        if ($rr->isRequestAjaxPost())
        {
            if ($v3toysOrder->load(\Yii::$app->request->post()) && $v3toysOrder->save())
            {
                foreach (\Yii::$app->shop->shopFuser->shopBaskets as $shopBasket)
                {
                    $shopBasket->delete();
                }

                /*try
                {
                    \Yii::$app->mailer->view->theme->pathMap['@app/mail'][] = '@v3toys/skeeks/mail';

                    \Yii::$app->mailer->compose('create-order', [
                        'model'  => $v3toysOrder
                    ])
                        ->setFrom([\Yii::$app->cms->adminEmail => \Yii::$app->cms->appName . ''])
                        ->setTo($v3toysOrder->email)
                        ->setSubject(\Yii::$app->cms->appName . ': ' . \Yii::t('skeeks/shop/app', 'New order') .' #' . $v3toysOrder->id)
                        ->send();

                } catch (\Exception $e)
                {
                    \Yii::error('Email submit error: ' . $e->getMessage());
                }*/

                $rr->message = 'Заказ успешно создан';
                $rr->success = true;
                $rr->redirect = Url::to(['/v3toys/cart/finish', 'key' => $v3toysOrder->key]);

            } else
            {
                $rr->message = 'Проверьте правильность заполнения полей';
                $rr->success = false;
            }

            return $rr;
        }

        return $this->render($this->action->id, [
            'model' => $v3toysOrder
        ]);
    }


    /**
     * @return string
     */
    public function actionCheckoutValidate()
    {
        $rr = new RequestResponse();
        $v3toysOrder = V3toysOrder::createCurrent();
        return $rr->ajaxValidateForm($v3toysOrder);
    }

    /**
     * @return string
     */
    public function actionSaveSession()
    {
        $rr             = new RequestResponse();

        $v3toysOrder    = V3toysOrder::createCurrent();
        $v3toysOrder->setAttributes(\Yii::$app->request->post('V3toysOrder'), false);

        $v3toysOrder->saveToSession();

        return $rr;
    }

    /**
     * @return string
     */
    public function actionGetPrices()
    {
        $rr = new RequestResponse();
        $v3toysOrder = V3toysOrder::createCurrent();
        $v3toysOrder->setAttributes(\Yii::$app->request->post('V3toysOrder'), false);
        $rr->success = true;

        $rr->data = [
            'money' => ArrayHelper::merge($v3toysOrder->money->jsonSerialize(), ['convertAndFormat' => \Yii::$app->money->convertAndFormat($v3toysOrder->money)]),
            'moneyOriginal' => ArrayHelper::merge($v3toysOrder->moneyOriginal->jsonSerialize(), ['convertAndFormat' => \Yii::$app->money->convertAndFormat($v3toysOrder->moneyOriginal)]),
            'moneyDelivery' => ArrayHelper::merge($v3toysOrder->moneyDelivery->jsonSerialize(), ['convertAndFormat' => \Yii::$app->money->convertAndFormat($v3toysOrder->moneyDelivery)]),
            'moneyDiscount' => ArrayHelper::merge($v3toysOrder->moneyDiscount->jsonSerialize(), ['convertAndFormat' => \Yii::$app->money->convertAndFormat($v3toysOrder->moneyDiscount)]),
        ];

        return $rr;
    }


    /**
     * Получение данных по доставке
     *
     * @return RequestResponse
     */
    public function actionGetCurrentShipping()
    {
        $rr = new RequestResponse();
        $rr->success = true;

        $rr->data = [
            'shipping' => \Yii::$app->v3toysSettings->currentShipping
        ];

        return $rr;
    }
}
