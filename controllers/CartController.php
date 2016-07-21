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
use v3toys\skeeks\forms\CreateOrderForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

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
     */
    public function actionCheckout()
    {
        $this->view->title = \Yii::t('skeeks/shop/app', 'Checkout').' | '.\Yii::t('skeeks/shop/app', 'Shop');

        $modelForm = new CreateOrderForm();
        $modelForm->loadDefaultValues();

        $rr = new RequestResponse();

        if ($rr->isRequestAjaxPost())
        {
            if ($modelForm->load(\Yii::$app->request->post()) && $modelForm->validate())
            {
                //create order
            } else
            {
                $rr->message = 'Проверьте правильность заполнения полей';
                $rr->success = false;
            }

            return $rr;
        }

        return $this->render($this->action->id, [
            'model' => $modelForm
        ]);
    }


    /**
     * @return string
     */
    public function actionCheckoutValidate()
    {
        $rr = new RequestResponse();
        $modelForm = new CreateOrderForm();
        return $rr->ajaxValidateForm($modelForm);
    }
}
