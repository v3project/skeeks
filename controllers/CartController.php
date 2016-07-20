<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\controllers;
use skeeks\cms\base\Controller;
use v3toys\skeeks\forms\CreateOrderForm;

/**
 * Class CartController
 * @package v3toys\skeeks\controllers
 */
class CartController extends Controller
{
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

        return $this->render($this->action->id, [
            'model' => $modelForm
        ]);
    }
}
