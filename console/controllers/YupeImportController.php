<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\console\controllers;

use Aws\CloudFront\Exception\Exception;
use skeeks\cms\models\CmsContent;
use skeeks\cms\models\CmsContentPropertyEnum;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopProduct;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\web\CookieCollection;

/**
 * Разовый скрипт импорта товаров с yupe
 *
 * Class AgentsController
 * @package v3toys\skeeks\console\controllers
 */
class YupeImportController extends Controller
{

    /**
     * @var string Сайт yupe на котором товары
     */
    public $yupeSite            = 'http://furreal.ru';

    /**
     * @var string Админ сайта
     */
    public $yupeLogin           = 'admin';

    /**
     * @var string Пароль админа
     */
    public $yupePassword        = 'qwe12345';

    /**
     * @var string Если есть basic auth, login
     */
    public $httpAuthLogin       = 'furreal';

    /**
     * @var string Если есть basic auth, password
     */
    public $httpAuthPassword    = 'PazzFrr';


    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
                'yupeSite',
                'yupeLogin',
                'yupePassword',
                'httpAuthLogin',
                'httpAuthPassword',
        ]);
    }

    /**
     * @inheritdoc
     * @since 2.0.8
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            's' => 'yupeSite',
            'l' => 'yupeLogin',
            'p' => 'yupePassword',
        ]);
    }

    /**
     * Импортировать товары
     */
    public function actionRun()
    {
        $products       = $this->_getProducts();
        $total          = count($products);

        $this->stdout("Import products from yupe: {$total}\n", Console::BOLD);
        Console::startProgress(0, $total);
        $counter = 0;
        foreach ($products as $product)
        {
            $counter ++;
            Console::updateProgress($counter, $total);

            $name       = ArrayHelper::getValue($product, 'name');
            $v3toysId   = ArrayHelper::getValue($product, 'external_id');


            $this->_addProduct($product);

            //sleep(1);
        }

        Console::endProgress();
    }

    /**
     * @param array $product
     */
    protected function _addProduct($product = [])
    {
        $this->stdout("\t{$v3toysId} - {$name}\n");

        try
        {
            $cmsContentElement = $this->_getCmsContentElementProduct($product);
        } catch (\Exception $e) {
            $this->stdout("\t{$e->getMessage()}'\n", Console::FG_RED);
            return $this;
        }

        if (!$cmsContentElement)
        {
            return false;
        }

        //Обновление цены и количества
        $this->_updateShopProduct($product, $cmsContentElement);

        //Основная информация
        $cmsContentElement->meta_title = ArrayHelper::getValue($product, 'meta_title');
        $cmsContentElement->meta_description = ArrayHelper::getValue($product, 'meta_description');
        $cmsContentElement->meta_keywords = ArrayHelper::getValue($product, 'meta_keywords');
        $cmsContentElement->description_short = ArrayHelper::getValue($product, 'short_description');
        $cmsContentElement->description_full = ArrayHelper::getValue($product, 'description');

        $cmsContentElement->save();

        //Главное изображение если еще не задано изображения
        if (ArrayHelper::getValue($product, 'image') && !$cmsContentElement->image) {
            try {
                $realUrl = $this->yupeSite . '/uploads/store/product/' . ArrayHelper::getValue($product, 'image');
                $file = \Yii::$app->storage->upload($realUrl, [
                    'name' => $cmsContentElement->name
                ]);
                $cmsContentElement->link('image', $file);
                $this->stdout("\tadded main image\n", Console::FG_GREEN);

            } catch (\Exception $e) {
                $message = 'Not upload image to: ' . $cmsContentElement->id . " ({$realUrl})";
                $this->stdout("\t{$message}\n", Console::FG_RED);
            }
        }


        //Дополнительные данные для разного проекта разные

        $this->_updateAdditionalProduct($product, $cmsContentElement);

    }

    protected function _updateAdditionalProduct($product = [], ShopCmsContentElement $cmsContentElement)
    {
        if ($cmsContentElement->relatedPropertiesModel->hasAttribute('sku') && ArrayHelper::getValue($product, 'sku'))
        {
            $cmsContentElement->relatedPropertiesModel->setAttribute('sku', ArrayHelper::getValue($product, 'sku'));
        }

        if ($cmsContentElement->relatedPropertiesModel->hasAttribute('material') && ArrayHelper::getValue($product, 'material'))
        {
            $cmsContentElement->relatedPropertiesModel->setAttribute('material', ArrayHelper::getValue($product, 'material'));
        }

        if ($cmsContentElement->relatedPropertiesModel->hasAttribute('size') && ArrayHelper::getValue($product, 'razm'))
        {
            $cmsContentElement->relatedPropertiesModel->setAttribute('size', ArrayHelper::getValue($product, 'razm'));
        }
        if ($cmsContentElement->relatedPropertiesModel->hasAttribute('sizeBox') && ArrayHelper::getValue($product, 'razmer-upakovki'))
        {
            $cmsContentElement->relatedPropertiesModel->setAttribute('sizeBox', ArrayHelper::getValue($product, 'razmer-upakovki'));
        }
        if ($cmsContentElement->relatedPropertiesModel->hasAttribute('energy') && ArrayHelper::getValue($product, 'batareyki'))
        {
            $cmsContentElement->relatedPropertiesModel->setAttribute('energy', ArrayHelper::getValue($product, 'batareyki'));
        }

        if ($cmsContentElement->relatedPropertiesModel->hasAttribute('complect') && ArrayHelper::getValue($product, 'komplekt'))
        {
            $cmsContentElement->relatedPropertiesModel->setAttribute('complect', ArrayHelper::getValue($product, 'komplekt'));
        }

        if ($cmsContentElement->relatedPropertiesModel->hasAttribute('age') && ArrayHelper::getValue($product, 'vozrast'))
        {

            if ($property = $cmsContentElement->relatedPropertiesModel->getRelatedProperty('age')) {
                $age = ArrayHelper::getValue($product, 'vozrast');

                if ($enum = $property->getEnums()->andWhere(['value' => $age])->one()) {

                } else {
                    $enum = new CmsContentPropertyEnum();
                    $enum->value        = (string) $age;
                    $enum->property_id  = $property->id;
                    if ($enum->save()) {
                        $this->stdout("\tadded age enum '{$age}' to DB\n", Console::FG_GREEN);

                    } else {
                        $error = Json::encode($enum->getFirstErrors());
                        $this->stdout("\tNot added age enum '{$age}' to DB: {$error}\n", Console::FG_RED);

                    }

                }
                if ($enum && !$enum->isNewRecord)
                {
                    $cmsContentElement->relatedPropertiesModel->setAttribute('age', $enum->id);

                }
            }
        }

        $cmsContentElement->relatedPropertiesModel->setAttribute('brand', '12764');

        if ($cmsContentElement->relatedPropertiesModel->save())
        {
            $this->stdout("\tSaved additionals\n", Console::FG_GREEN);
        } else
        {
            $error = Json::encode($cmsContentElement->relatedPropertiesModel->getFirstErrors());
            $this->stdout("\tNot saved additionals: {$error}\n", Console::FG_RED);
        }
    }

    protected function _updateShopProduct($product = [], ShopCmsContentElement $cmsContentElement)
    {
        $shopProduct = $cmsContentElement->shopProduct;
        if (!$shopProduct)
        {
            $shopProduct = new ShopProduct();
            $shopProduct->id = $cmsContentElement->id;
            $shopProduct->save();
            $this->stdout("\tadded ShopProduct\n", Console::FG_GREEN);

        } else {
            $this->stdout("\texist ShopProduct\n");

        }

        $shopProduct->baseProductPriceValue = ArrayHelper::getValue($product, 'price');
        $shopProduct->baseProductPriceCurrency = "RUB";

        $shopProduct->purchasing_price = ArrayHelper::getValue($product, 'purchase_price');
        $shopProduct->purchasing_currency = "RUB";

        $shopProduct->quantity = (int) ArrayHelper::getValue($product, 'quantity');
        if ($shopProduct->save())
        {
            $this->stdout("\tShopProduct saved: quantity={$shopProduct->quantity}; price={$shopProduct->baseProductPriceValue}\n", Console::FG_GREEN);

        } else
        {
            $this->stdout("\tShopProduct not saved\n", Console::FG_RED);
        }
    }

    /**
     * @param array $product
     * @return array|null|ShopCmsContentElement|\yii\db\ActiveRecord
     * @throws \skeeks\cms\relatedProperties\models\InvalidParamException
     */
    protected function _getCmsContentElementProduct($product = [])
    {
        $cmsContent = ShopCmsContentElement::find()
            ->joinWith('relatedElementProperties map')
            ->joinWith('relatedElementProperties.property property')
            ->andWhere(['property.code' => \Yii::$app->v3toysSettings->v3toysIdPropertyName])
            ->andWhere(['map.value' => ArrayHelper::getValue($product, 'external_id') ])
            ->joinWith('cmsContent as ccontent')
            ->andWhere(['ccontent.code' => 'product'])
            ->one();

        if (!$cmsContent)
        {
            $content = CmsContent::findOne(['code' => 'product']);
            //Иначе создадим
            $cmsContent = new ShopCmsContentElement();
            //$cmsContent->tree_id = ImportStockSaleV2::ROOT_TREE_ID; //Каталог
            $cmsContent->content_id = CmsContent::findOne(['code' => 'product'])->id; //Тип контента
            $cmsContent->name = ArrayHelper::getValue($product, 'name');

            if ($cmsContent->save())
            {
                $cmsContent->relatedPropertiesModel->setAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName, ArrayHelper::getValue($product, 'external_id'));
                $cmsContent->relatedPropertiesModel->save(false);

            } else
            {
                throw new Exception("Not created product: " . serialize($cmsContent->getFirstErrors()));
            }

            $this->stdout("\tCreated element {$cmsContent->id}\n");
            return $cmsContent;

        }

        $this->stdout("\tExist element {$cmsContent->id}\n");
        return $cmsContent;

    }

    /**
     * @return array
     */
    protected function _getProducts()
    {
        /*$client = $this->_createHttpClient();

        $request = $client->post('backend/login', [
            'LoginForm' =>
            [
                'email'     => $this->yupeLogin,
                'password'  => $this->yupePassword,
            ],
        ]);

        $this->_httpAuth($request);
        $loginResponse = $request->send();*/


        $client     = $this->_createHttpClient();
        $request2   = $client->get('backend/store/product/exportjson/')
                        //->setCookies($loginResponse->cookies)
        ;

        $this->_httpAuth($request2);
        $request2->addHeaders([
            'Cookie' => 'YUPE_TOKEN=7d8ffa6b5a98b6905e35456cffb622a978b8a173s%3A40%3A%224ee84dcbabd0a1857daac48491638fa468579140%22%3B; PHPSESSID=doncpfguq7vgnas52439vcv2d0; 59249ba4b622b81d4b624a773379de0f=9aed9897e389284a2b834ed9725cd73bb39554c1s%3A146%3A%22b00d2395c278b1daf793c80aa836fb68b7f1ccc5a%3A4%3A%7Bi%3A0%3Bs%3A1%3A%223%22%3Bi%3A1%3Bs%3A5%3A%22admin%22%3Bi%3A2%3Bi%3A604800%3Bi%3A3%3Ba%3A1%3A%7Bs%3A2%3A%22at%22%3Bs%3A32%3A%22A2xBppokQT8RIgD_rUYYw4m2qysDNT6B%22%3B%7D%7D%22%3B; language=57e4c243f03feb8e3223fe2dfd2328d0f5fc69dcs%3A2%3A%22ru%22%3B',
        ]);

        $response = $request2->send();

        if ($response->isOk)
        {
            return (array) Json::decode($response->content);
        }

        return [];
    }

    /**
     * @param Client $client
     * @return $this
     */
    protected function _httpAuth(Request $request)
    {
        if ($this->httpAuthLogin && $this->httpAuthPassword)
        {
            $code = base64_encode("$this->httpAuthLogin:$this->httpAuthPassword");
            $request->addHeaders(['Authorization' => 'Basic ' . $code]);
        }

        return $this;
    }

    /**
     * @return Client
     */
    protected function _createHttpClient()
    {
        return new Client(['baseUrl' => $this->yupeSite]);
    }



}
