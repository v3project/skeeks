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
use v3toys\skeeks\models\V3toysOrder;
use v3toys\skeeks\models\V3toysOrderStatus;
use v3toys\skeeks\models\V3toysProductProperty;
use v3toys\skeeks\models\V3toysShippingCity;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;

/**
 * Class InitV2Controller
 *
 * @package v3toys\skeeks\console\controllers
 */
class InitV2Controller extends Controller
{

    /**
     *
     */
    public function actionProducts()
    {
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
            foreach (ShopCmsContentElement::find()->where(['content_id' => $contentIds])->each(10) as $element) {

                $this->stdout("\t{$element->id}: {$element->name}\n");

                $v3id = $element->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName);
                if ($v3id)
                {
                    if (V3toysProductProperty::find()->where(['v3toys_id' => $v3id])->one())
                    {
                        $this->stdout("\t\t Property exist\n", Console::FG_YELLOW);
                        continue;
                    }
                    
                    $property                       = new V3toysProductProperty();
                    $property->v3toys_id            = $v3id;
                    $property->id                   = $element->id;
                    if ($property->save())
                    {
                        $this->stdout("\t\t Property added\n", Console::FG_GREEN);
                    } else
                    {
                        $this->stdout("\t\t Property not added\n", Console::FG_RED);
                        print_r($property->errors);
                        die;
                    }
                }
            }
        }
    }

    public function actionInfo()
    {
        $query = (new \yii\db\Query())
                    ->from('apiv5.affiliate')
                    ->indexBy('id');

        $prices = $query->all(\Yii::$app->dbV3project);

        print_r($prices);die;
    }
}
