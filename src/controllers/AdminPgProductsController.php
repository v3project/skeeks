<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */

namespace v3toys\skeeks\controllers;

use skeeks\cms\components\marketplace\models\PackageModel;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\Comment;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\controllers\AdminController;
use skeeks\cms\modules\admin\controllers\AdminModelEditorController;
use v3toys\skeeks\models\v5api\PgProductModel;
use Yii;
use skeeks\cms\models\User;
use skeeks\cms\models\searchs\User as UserSearch;
use yii\httpclient\Client;

/**
 * Class AdminPgProductsController
 * @package v3toys\skeeks\controllers
 */
class AdminPgProductsController extends AdminModelEditorController
{
    public function init()
    {
        $this->name = \Yii::t('v3toys/skeeks', 'Все товары');
        $this->modelClassName = PgProductModel::class;
        $this->modelPkAttribute = 'id';
        $this->modelShowAttribute = "id";
        parent::init();
    }

    public function actionAdd()
    {
        $rr = new RequestResponse();

        $id = \Yii::$app->request->post('id');
        $action = \Yii::$app->request->post('action');

        if (!$id && !$action) {
            return false;
        }

        $insert_type = 3; //Все характеристики

        if ($action == 'text') {
            $insert_type = 2; //Все характеристики
        }

        if ($action == 'prop') {
            $insert_type = 1; //Все характеристики
        }


        $request = [
            'aff_key' => \Yii::$app->v3toysSettings->affiliate_key,
            'data' => []
        ];

        $request['data'][] = [
            'product_id' => $id,
            'title' => '',
            'sku' => '',
            'v3project_id' => $id,
            'barcode' => '',
            'brand' => '',
            'price' => '',
            'insert_type' => $insert_type,
        ];

        $url = 'http://back.v3project.ru/index.php?r=contents/api/v1/products/sendproducts';
        //$url = 'http://back.v3project.ru.vps108.s2.h.skeeks.com/index.php?r=contents/api/v1/products/sendproducts';

        $client = new Client([
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ]
        ]);

        $httpRequest = $client->createRequest()
            ->setMethod("POST")
            ->setUrl($url)
            ->addHeaders(['Content-type' => 'application/json'])
            ->addHeaders(['user-agent' => 'JSON-RPC PHP Client'])
            ->setData($request)
            ->setOptions([
                'timeout' => 30
            ]);

        $httpRequest->send();
    }
}
