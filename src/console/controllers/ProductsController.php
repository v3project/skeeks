<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\console\controllers;

use skeeks\cms\relatedProperties\propertyTypes\PropertyTypeText;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopPersonTypeProperty;
use skeeks\cms\shop\models\ShopProduct;
use skeeks\cms\shop\models\ShopProductPrice;
use v3toys\skeeks\models\V3toysProductContentElement;
use v3toys\skeeks\models\V3toysOrder;
use v3toys\skeeks\models\V3toysOrderStatus;
use v3toys\skeeks\models\V3toysShippingCity;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\httpclient\Client;

/**
 * Class ProductsController
 * @package v3toys\skeeks\console\controllers
 */
class ProductsController extends Controller
{
    /**
     * Обновление цен и налчия товаров
     */
    public function actionLoad()
    {
        ini_set("memory_limit","8192M");
        set_time_limit(0);


        $query = (new \yii\db\Query())
                    ->from('apiv5.affproduct');

        $prices = $query->all(\Yii::$app->dbV3project);

        print_r($prices);die;



        $contentIds = (array) \Yii::$app->v3toysSettings->content_ids;
        if (!$contentIds)
        {
            $this->stdout("Не настроен v3toys комонент: {$total}\n", Console::FG_RED);
            return;
        }

        $count = V3toysProductContentElement::find()->where(['content_id' => $contentIds])->count();
        $this->stdout("Всего товаров: {$count}\n", Console::BOLD);

        if ($count)
        {
            $i      = 0;
            $page   = 0;
            $step   = 1000;

            $pages = round($count/$step);
            if ($pages == 0)
            {
                $pages = 1;
            }

            $this->stdout("Всего страниц: {$pages}\n");
            sleep(1);

            for($i >= 0; $i <= $count; $i ++)
            {
                if ($i % $step == 0)
                {

                    $this->stdout("\tСтраница: {$page}\n");

                    $elements = V3toysProductContentElement::find()
                        ->where(['content_id' => $contentIds])
                        ->orderBy(['id' => SORT_ASC])
                        ->with('v3toysProductProperty')
                        ->limit($step)
                        ->offset($step * $page);

                    if ($elementsUpdate = $elements->all())
                    {
                        $this->stdout('found: ' . count($elementsUpdate));
                        $this->_updateElements($elementsUpdate);
                    } else
                    {
                        $this->stdout('not found');
                    }

                    $page = $page + 1;

                    //print_r(count($elements->all()));
                    //print_r($elements->createCommand()->rawSql);
                    //print_r($page);
                    //echo "\n";
                }

            }
        }
    }

}
