<?php
/**
 * TODO: добавить режим -f = которые все жестко перезапишет
 *
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
     * @var bool
     * 1, будут перезаписаны все характеристики name, meta, description
     * 0, характеристики не будут перезаписаны, будут дописаны если еще нет на сайте, например описание
     */
    public $rewriteInfo = false;

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['rewriteInfo'] // global for all actions
        );
    }

    /**
     * @inheritdoc
     * @since 2.0.8
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'r' => 'rewriteInfo',
        ]);
    }

    /**
     * Обновление цен и налчия товаров
     */
    public function actionLoad()
    {
        ini_set("memory_limit","8192M");
        set_time_limit(0);

        if (!$this->rewriteInfo)
        {
            $this->stdout("Безопасный режим\n", Console::FG_GREEN);
        } else
        {
            $this->stdout("Не безопасный режим\n", Console::FG_RED);
            $this->stdout("\tМогут быть перезаписаны все данные\n");
            sleep(5);
        }

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
                    //->andWhere(["!=", 'description', ""])
                    //->andWhere(["!=", 'main_image_path', ""])
                    //->andWhere(["!=", 'second_image_paths', ""])
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

            if (!$element = V3toysProductContentElement::find()->joinWith('v3toysProductProperty as p')->andWhere(['p.v3toys_id' => $v3id])->one())
            {
                $this->stdout("\t\t new product: {$element->id}\n", Console::FG_GREEN);

                $property                       = new V3toysProductProperty();
                $property->consoleController    = $this;
                $property->v3toys_id            = ArrayHelper::getValue($row, 'product_id');


                $element = new V3toysProductContentElement();
                $element->content_id    = $contentId;

                $this->_loadElementInfo($element, $row);

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

                    if ($element->shopProduct)
                    {
                        $element->shopProduct->quantity = 0;
                        $element->shopProduct->save();
                    } else
                    {
                        $shopProduct = new ShopProduct([
                            'id' => $element->id,
                            'quantity' => 0
                        ]);

                        if ($shopProduct->save())
                        {
                            $this->stdout("\t\t\tShopProduct created\n");
                        } else
                        {
                            $this->stdout("\t\t\tShopProduct not created\n", Console::FG_RED);
                        }
                    }


                } else
                {
                    $this->stdout("\t\t Element not added\n", Console::FG_RED);
                    print_r($element->errors);
                }

            } else
            {
                $this->stdout("\t\t exist site id: {$element->id}\n", Console::FG_YELLOW);
                $this->_loadElementInfo($element, $row);
                $element->save();
            }


            $this->_imageUpdate($element, $row);
            $this->_imagesUpdate($element, $row);
        }
    }

    /**
     * @param $element
     * @param $row
     * @return $this
     */
    protected function _loadElementInfo($element, $row)
    {
        //Можно перезаписать все без разбора
        if ($this->rewriteInfo)
        {
            $element->meta_keywords = (string) ArrayHelper::getValue($row, 'meta_keywords');
            $element->meta_title = (string) ArrayHelper::getValue($row, 'meta_title');
            $element->meta_description = (string) ArrayHelper::getValue($row, 'meta_description');
            $element->name = (string) ArrayHelper::getValue($row, 'title');
            $element->description_full = (string) ArrayHelper::getValue($row, 'description');
        } else
        {
            if (!$element->meta_keywords)
            {
                $element->meta_keywords = (string) ArrayHelper::getValue($row, 'meta_keywords');
            }

            if (!$element->meta_title)
            {
                $element->meta_title = (string) ArrayHelper::getValue($row, 'meta_title');
            }

            if (!$element->meta_description)
            {
                $element->meta_description = (string) ArrayHelper::getValue($row, 'meta_description');
            }


            if (!$element->name)
            {
                $element->name = (string) ArrayHelper::getValue($row, 'title');
            }

            if (!$element->description_full)
            {
                $element->description_full = (string) ArrayHelper::getValue($row, 'description');
            }
        }

        return $this;
    }

    protected function _imagesUpdate($element, $row)
    {
        $imagePatternPath = ArrayHelper::getValue($this->affData, 'affproduct_image_url_pattern');

        //Работа с главным изображением
        $second_image_paths = ArrayHelper::getValue($row, 'second_image_paths');

        if ($second_image_paths && !$element->images && $imagePatternPath)
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

        if ($mainImage && $imagePatternPath && !$element->image)
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
