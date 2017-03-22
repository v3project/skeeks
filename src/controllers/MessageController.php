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
use v3toys\skeeks\models\V3toysMessage;
use v3toys\skeeks\models\V3toysOrder;
use v3toys\skeeks\V3toysModule;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Class CartController
 * @package v3toys\skeeks\controllers
 */
class MessageController extends Controller
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
                    'submit'  => ['post'],
                    'form-submit'  => ['post'],
                    'form-validate'  => ['post'],
                ],
            ],
        ]);
    }

    /**
     * @return RequestResponse
     */
    public function actionSubmit()
    {
        $rr = new RequestResponse();

        $v3toysMessage = new V3toysMessage();
        $v3toysMessage->loadDefaultValues();

        try
        {
            if ($elementId = \Yii::$app->request->post('element_id'))
            {
                $v3toysMessage->addProduct($elementId);
            }

            if ($value = \Yii::$app->request->post('full_name'))
            {
                $v3toysMessage->full_name = $value;
            }

            if ($email = \Yii::$app->request->post('email'))
            {
                $v3toysMessage->email = $email;
            }

            if ($phone = \Yii::$app->request->post('phone'))
            {
                $v3toysMessage->phone = $phone;
            }

            if ($comment = \Yii::$app->request->post('comment'))
            {
                $v3toysMessage->comment = $comment;
            }

            if ($v3toysMessage->save())
            {
                $rr->success = true;
                $rr->message = "Ожидайте звонка";
            } else
            {
                throw new Exception('Не удалось сохранить: ' . Json::encode($v3toysMessage->firstErrors));
            }


        } catch (\Exception $e)
        {
            $rr->success = false;
            $rr->message = $e->getMessage();
        }


        return $rr;
    }

    /**
     * @return RequestResponse
     */
    public function actionFormSubmit()
    {
        $rr = new RequestResponse();

        $v3toysMessage = new V3toysMessage();
        $v3toysMessage->loadDefaultValues();

        try
        {
            if ($rr->isRequestAjaxPost())
            {
                if ($elementId = \Yii::$app->request->post('element_id'))
                {
                    $v3toysMessage->addProduct($elementId);
                }

                if ($v3toysMessage->load(\Yii::$app->request->post()) && $v3toysMessage->save())
                {
                    $rr->success = true;
                    $rr->message = "Ожидайте звонка";
                }
            }
        } catch (\Exception $e)
        {
            $rr->success = false;
            $rr->message = $e->getMessage();
        }


        return $rr;
    }
    /**
     * @return RequestResponse
     */
    public function actionFormValidate()
    {
        $v3toysMessage = new V3toysMessage();
        $v3toysMessage->loadDefaultValues();

        if ($elementId = \Yii::$app->request->post('element_id'))
        {
            $v3toysMessage->addProduct($elementId);
        }

        $rr = new RequestResponse();
        return $rr->ajaxValidateForm($v3toysMessage);
    }
}
