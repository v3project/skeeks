<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\console\controllers;

use skeeks\cms\components\Cms;
use skeeks\cms\relatedProperties\propertyTypes\PropertyTypeText;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopPersonTypeProperty;
use skeeks\cms\shop\models\ShopProduct;
use skeeks\cms\shop\models\ShopProductPrice;
use v3toys\skeeks\models\V3toysProductContentElement;
use v3toys\skeeks\models\V3toysOrder;
use v3toys\skeeks\models\V3toysOrderStatus;
use v3toys\skeeks\models\V3toysProductProperty;
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
    protected $affData = [];

    /**
     * Обновление цен и налчия товаров
     */
    public function actionLoad()
    {
        ini_set("memory_limit","8192M");
        set_time_limit(0);


        $contentIds = (array) \Yii::$app->v3toysSettings->content_ids;
        if (!$contentIds)
        {
            $this->stdout("Не настроен v3toys комонент: {$total}\n", Console::FG_RED);
            return;
        }

        $contentId = $contentIds[0];
        
        $queryAff = (new \yii\db\Query())
                    ->from('apiv5.affiliate')
        ;

        $this->affData = $queryAff->one(\Yii::$app->dbV3project);

        if (!$this->affData)
        {
            $message = 'Нет данных по аффилиату';
            $this->stdout("$message\n", Console::FG_RED);
            return;
        }
        
        $query = (new \yii\db\Query())
                    ->from('apiv5.affproduct')
                    ->where(["!=", 'title', ""])
                    ->andWhere(["!=", 'description', ""])
                    ->andWhere(["!=", 'main_image_path', ""])
                    ->andWhere(["!=", 'second_image_paths', ""])
        ;

        $count = $query->count("*", \Yii::$app->dbV3project);
        $this->stdout("Всего товаров: {$count}\n", Console::BOLD);
        /**
         * Array
(
    [id] => 55130
    [created_at] => 2016-08-22 17:03:49
    [updated_at] => 2016-09-27 00:10:30
    [affiliate_id] => 2
    [fr_aff_key] => 252821
    [product_id] => 324164
    [title] => Пластиковый конструктор "Мусороуборочная машина", 102 детали
    [alt_title] =>
    [description] => <p>тестовый текст от Коваленко</p>
    [meta_title] => МЕТА заголовок товара
    [meta_keywords] =>
    [meta_description] => МЕТА описание
    [main_image_path] => 57e18d7687fc5.jpg
    [second_image_paths] =>
)

         */
        foreach ($query->orderBy(['id' => SORT_DESC])->each(100, \Yii::$app->dbV3project) as $row)
        {
            //print_r($row);die;

            $v3id   = ArrayHelper::getValue($row, 'product_id');
            $id     = ArrayHelper::getValue($row, 'id');
            if (!$v3id)
            {
                $this->stdout("\t\t Not found v3id\n", Console::FG_RED);
                continue;
            }

            $this->stdout("\t\t V3productId: {$v3id}\n");
            $this->stdout("\t\t Affiliat Id: {$id}\n");

            if ($element = V3toysProductContentElement::find()->joinWith('v3toysProductProperty as p')->andWhere(['p.v3toys_id' => $v3id])->one())
            {
                $this->stdout("\t\t exist: {$v3id}\n", Console::FG_YELLOW);

                $element->meta_keywords = (string) ArrayHelper::getValue($row, 'meta_title');
                $element->meta_title = (string) ArrayHelper::getValue($row, 'meta_title');
                $element->meta_description = (string) ArrayHelper::getValue($row, 'meta_description');
                $element->name = (string) ArrayHelper::getValue($row, 'title');
                $element->description_full = (string) ArrayHelper::getValue($row, 'description');
                $element->save();
                
                $this->_imageUpdate($element, $row);
                $this->_imagesUpdate($element, $row);
                
                continue;
            }


            $property                       = new V3toysProductProperty();
            $property->consoleController    = $this;
            $property->v3toys_id            = ArrayHelper::getValue($row, 'product_id');


            $element = new V3toysProductContentElement();
            $element->content_id    = $contentId;
            $element->meta_keywords = (string) ArrayHelper::getValue($row, 'meta_title');
            $element->meta_title = (string) ArrayHelper::getValue($row, 'meta_title');
            $element->meta_description = (string) ArrayHelper::getValue($row, 'meta_description');
            $element->name = (string) ArrayHelper::getValue($row, 'title');
            $element->description_full = (string) ArrayHelper::getValue($row, 'description');

            if ($element->save())
            {
                $this->stdout("\t\t Element added\n", Console::FG_GREEN);
                $property->id = $element->id;

                if ($property->save())
                {
                    $this->stdout("\t\t Property added\n", Console::FG_GREEN);
                } else
                {
                    $this->stdout("\t\t Property not added\n", Console::FG_RED);
                    if ($element->delete())
                    {
                        $this->stdout("\t\t Element deleted\n");
                    }
                }

            } else
            {
                $this->stdout("\t\t Element not added\n", Console::FG_RED);
                print_r($element->errors);
            }





            
            $this->_imageUpdate($element, $row);
            $this->_imagesUpdate($element, $row);
            
            die;
        }
    }
    
    
    protected function _imagesUpdate($element, $row)
    {
        $imagePatternPath = ArrayHelper::getValue($this->affData, 'affproduct_image_url_pattern');

        //Работа с главным изображением
        $second_image_paths = ArrayHelper::getValue($row, 'second_image_paths');

        if ($second_image_paths && !$element->images)
        {
            $second_image_paths = Json::decode($second_image_paths);
            $this->stdout("\t\t Загрузка картинок\n", Console::FG_GREEN);

            foreach ((array) $second_image_paths as $imageName)
            {
                $realImagePath = str_replace('{image_path}', $imageName, $imagePatternPath);
                
                try
                {
                    $this->stdout("\t\t\t Загрузка картинки: {$realImagePath}\n", Console::FG_GREEN);

                    $file = \Yii::$app->storage->upload($realImagePath, [
                        'name' => $element->name
                    ]);

                    $element->link('images', $file);
                    $file->original_name = $imageName;
                    $file->save();

                    $this->stdout("\t\t\t Загружено\n", Console::FG_GREEN);
                } catch (\Exception $e)
                {
                    $message = $e->getMessage();
                    $this->stdout("\t\t\t {$message}\n", Console::FG_RED);
                }
            }
        }
        
        return $this;
    }
    
    protected function _imageUpdate($element, $row)
    {
        $imagePatternPath = ArrayHelper::getValue($this->affData, 'affproduct_image_url_pattern');

        //Работа с главным изображением
        $mainImage = ArrayHelper::getValue($row, 'main_image_path');

        if ($mainImage)
        {
            $realImagePath = str_replace('{image_path}', $mainImage, $imagePatternPath);

            //Если нет картинки
            if (!$element->image)
            {
                try
                {
                    $this->stdout("\t\t Загрузка картинки: {$realImagePath}\n");
                    $file = \Yii::$app->storage->upload($realImagePath, [
                        'name' => $element->name
                    ]);

                    $element->link('image', $file);
                    $file->original_name = $mainImage;
                    $file->save();

                    $this->stdout("\t\t\t Загружено\n", Console::FG_GREEN);
                } catch (\Exception $e)
                {
                    $message = $e->getMessage();
                    $this->stdout("\t\t\t {$message}\n", Console::FG_RED);
                }

            } else
            {

                if ($element->image->original_name != $mainImage)
                {
                    try
                    {
                        $this->stdout("\t\t Главная картинка изменилась\n", Console::FG_YELLOW);
                        $this->stdout("\t\t Загрузка картинки\n");

                        $file = \Yii::$app->storage->upload($realImagePath, [
                            'name' => $element->name
                        ]);

                        $element->link('image', $file);
                        $file->original_name = $mainImage;
                        $file->save();

                        $this->stdout("\t\t\t Загружено\n", Console::FG_GREEN);
                    } catch (\Exception $e)
                    {
                        $message = $e->getMessage();
                        $this->stdout("\t\t\t {$message}\n", Console::FG_RED);
                    }
                }
            }
        }
        
        return $this;
    }

}
