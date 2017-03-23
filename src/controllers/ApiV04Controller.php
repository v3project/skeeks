<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 14.12.2016
 */
namespace v3toys\skeeks\controllers;

use v3toys\skeeks\models\V3toysMessage;
use v3toys\skeeks\models\V3toysOrder;
use v3toys\skeeks\models\V3toysProductContentElement;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * @see http://www.v3toys.ru/index.php?nid=api;
 *
 * Class ApiV04Controller
 * @package v3toys\skeeks\controllers
 */
class ApiV04Controller extends Controller
{
    public $version         = '0.4';
    public $defaultAction   = 'request';

    protected function verbs()
    {
        return [
            'request' => ['post']
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['AccessControl'] = [
            'class' => AccessControl::className(),
            'denyCallback' => function($rule, $action)
            {
                throw new ForbiddenHttpException(\Yii::t('yii', 'You are not allowed to perform this action.'));
            },
            'rules' => [
                [
                    'allow' => true,
                    'ips' => \Yii::$app->v3toysSettings->api_allow_ids
                ],
            ]
        ];

        return $behaviors;
    }

    /**
     * @var string
     */
    protected $_currentMethod = '';

    /**
     * @return static
     * @throws NotFoundHttpException
     */
    public function actionRequest()
    {
        $data = [];

        try
        {
            $rawBody = trim(\Yii::$app->request->rawBody);
            if (!$rawBody)
            {
                throw new Exception('Не указаны данные в запросе.');
            }

            $requestData = Json::decode($rawBody);

            $version    = (string) ArrayHelper::getValue($requestData, 'v');
            $method     = (string) ArrayHelper::getValue($requestData, 'method');
            $params     = (array) ArrayHelper::getValue($requestData, 'params');

            if (!$version || !$method || !array_key_exists('params', $requestData))
            {
                throw new Exception('Не указан один из обязательных параметров в запросе: http://www.v3toys.ru/index.php?nid=api (1.1 Описание общих полей запросов)');
            }

            if ($version != $this->version)
            {
                throw new Exception('Версии апи не совпадают.');
            }

            if (!method_exists($this, $method))
            {
                throw new Exception('Запрошенный метод апи не реализован.');
            }

            $this->_currentMethod = $method;
            $data = $this->{$method}($params);

        } catch (\Exception $e)
        {
            \Yii::$app->response->statusCode = 400;

            return [
                "v"                 => $this->version,
                "affiliate_key"     => \Yii::$app->v3toysSettings->affiliate_key,
                "method"            => $this->_currentMethod,
                "error_code"        => $e->getCode(),
                "error_message"     => $e->getMessage(),
                "error_data"        => [],
            ];
        }

        return [
            "v"                 => $this->version,
            "affiliate_key"     => \Yii::$app->v3toysSettings->affiliate_key,
            "method"            => $this->_currentMethod,
            "data"              => $data,
        ];
    }


    /**
     * 2.1.1 Метод getOrdersIdsByPeriod - получение списка номеров заказов за период времени
     *
     * @param array $params
     * @return array
     */
    public function getOrdersIdsByPeriod($params = [])
    {
        $ids = [];

        $find = V3toysOrder::find()->asArray()->indexBy('id');

        if ($start = ArrayHelper::getValue($params, 'start'))
        {
            $find->andWhere(['>=', 'created_at', strtotime($start)]);
        }

        if ($end = ArrayHelper::getValue($params, 'end'))
        {
            $find->andWhere(['<=', 'created_at', strtotime($start)]);
        }

        $orders = $find->all();
        if ($orders)
        {
            $ids = array_keys($orders);
        }

        return [
            'orders_ids' => $ids
        ];
    }

    /**
     * 2.1.2 Метод getOrderDataById - получение данных заказа по номеру
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getOrderDataById($params = [])
    {
        $orderId = ArrayHelper::getValue($params, 'order_id');
        if (!$orderId)
        {
            throw new Exception('Не передан обязательный параметр order_id');
        }

        /**
         * @var $v3toysOrder V3toysOrder
         */
        $v3toysOrder = V3toysOrder::findOne((int) $orderId);
        if (!$v3toysOrder)
        {
            throw new Exception("Заказ не найден");
        }

        return $v3toysOrder->getApiRequestData();
    }

    /**
     * 2.1.3 Метод getProductDataById - получение данных товара по идентификатору
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getProductDataById($params = [])
    {
        $product_id = ArrayHelper::getValue($params, 'product_id');
        if (!$product_id)
        {
            throw new Exception('Не передан обязательный параметр product_id.');
        }

        $element = V3toysProductContentElement::find()
            ->joinWith('v3toysProductProperty as p')
            ->where(['p.v3toys_id' => $product_id])
            ->one();

        if (!$element)
        {
            throw new Exception('Товар не найден или удален.');
        }

        return $this->_convertForApi($element);
    }

    /**
     * 2.1.4 Метод getProductsDataByIds - получение списка товаров по идентификаторам
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getProductsDataByIds($params = [])
    {
        $products_ids = (array) ArrayHelper::getValue($params, 'products_ids');
        if (!$products_ids)
        {
            throw new Exception('Не передан обязательный параметр products_ids');
        }

        $elements = V3toysProductContentElement::find()
            ->joinWith('v3toysProductProperty as p')
            ->where(['p.v3toys_id' => (array) $products_ids])
            ->all();

        if (!$elements)
        {
            throw new Exception('Товары не найдены или удалены.');
        }

        $result = [];

        foreach ($elements as $element)
        {
            $result[] = $this->_convertForApi($element);
        }
        return $result;
    }

    
    protected function _convertForApi(V3toysProductContentElement $element)
    {
        $images = [];

        if ($element->image)
        {
            $images[] = $element->image->absoluteSrc;
        }

        $data = [
            'product_id'    => (int) $element->v3toysProductProperty->v3toys_id,
            'title'         => $element->name,
            'url'           => $element->getUrl(true),
            'price'         => (float) $element->shopProduct->baseProductPrice->money->getValue(),
            'images'        => $images,
        ];

        return $data;
    }
    
    /**
     * 2.2.1 Метод getMessageIdsByPeriod - получение списка номеров заявок за период времени
     *
     * @return array
     */
    public function getMessageIdsByPeriod($params = [])
    {
        $ids = [];

        $find = V3toysMessage::find()->asArray()->indexBy('id');

        if ($start = ArrayHelper::getValue($params, 'start'))
        {
            $find->andWhere(['>=', 'created_at', strtotime($start)]);
        }

        if ($end = ArrayHelper::getValue($params, 'end'))
        {
            $find->andWhere(['<=', 'created_at', strtotime($start)]);
        }

        $orders = $find->all();
        if ($orders)
        {
            $ids = array_keys($orders);
        }

        return [
            'message_ids' => $ids
        ];

    }

    /**
     * 2.2.2 Метод getMessageDataById - получение данных заявки по номеру
     *
     * @return array
     */
    public function getMessageDataById($params = [])
    {
        $message_id = ArrayHelper::getValue($params, 'message_id');
        if (!$message_id)
        {
            throw new Exception('Не передан обязательный параметр message_id');
        }

        /**
         * @var $v3toysMessage V3toysMessage
         */
        $v3toysMessage = V3toysMessage::findOne((int) $message_id);
        if (!$v3toysMessage)
        {
            throw new Exception("Заявка не найдена");
        }

        return $v3toysMessage->getApiRequestData();
    }
}