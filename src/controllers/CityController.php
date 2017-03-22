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
use v3toys\skeeks\kiwi\CityStatic;
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
class CityController extends Controller
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
                    'save'  => ['post'],
                ],
            ],
        ]);
    }

    /**
     * @return RequestResponse
     */
    public function actionSave()
    {
        $rr = new RequestResponse();
        $title = \Yii::$app->request->post('title');
        $cities = CityStatic::$_city_static;

        if (!$title || !$cities)
        {
            $rr->success = false;
            $rr->message = 'Нет title или городов';
        }

        foreach ($cities as $city)
        {
            if ($city['title'] == $title)
            {
                //save $city
                $saveCity['value'] = ArrayHelper::getValue($city, 'title');
                $saveCity['unrestricted_value'] = ArrayHelper::getValue($city, 'unrestricted_value');
                $saveCity['data'] = ArrayHelper::getValue($city, 'data');
                $rr->success = true;
                \Yii::$app->dadataSuggest->saveAddress($saveCity);
                return $rr;
            }
        }

        return $rr;
    }
}
