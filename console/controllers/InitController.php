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
use v3toys\skeeks\models\V3toysOrderStatus;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;

/**
 * Настройка магазина
 * Class InitController
 *
 * @package v3toys\skeeks\console\controllers
 */
class InitController extends Controller
{

    /**
     * Инициализация возможных статусов заказов из api
     */
    public function actionOrderStatuses()
    {
        $response = \Yii::$app->v3toysApi->getStatus();

        if ($response->isError)
        {
            $this->stdout("Ошибка апи: {$response->error_message}\n", Console::FG_RED);
            return false;
        }

        if ($response->data)
        {
            $total = count($response->data);
            $this->stdout("Статусов в апи: {$total}\n", Console::BOLD);

            foreach ((array) $response->data as $statusData)
            {
                $name       = ArrayHelper::getValue($statusData, 'title');
                $v3toys_id  = ArrayHelper::getValue($statusData, 'id');

                if (V3toysOrderStatus::findOne(['v3toys_id' => $v3toys_id]))
                {
                    $this->stdout("\t {$name} - exist\n", Console::FG_YELLOW);

                } else
                {
                    $status             = new V3toysOrderStatus();
                    $status->name       = $name;
                    $status->v3toys_id  = $v3toys_id;

                    if ($status->save())
                    {
                        $this->stdout("\t {$name} - added\n", Console::FG_GREEN);
                    } else
                    {
                        $error = Json::encode($status->getFirstErrors());
                        $this->stdout("\t {$name} - not added: {$error}\n", Console::FG_RED);
                    }

                }
            }
        }

    }

    /**
     * Настройка и инициализация параметров покупателя
     * Команда создает недостающией свойства покупателя
     */
    public function actionUpdatePersonType()
    {
        if (!\Yii::$app->v3toysSettings->shopPersonType)
        {
            $this->stdout("Для начала выберите профиль в настройках компонента v3toys (через админку)\n", Console::FG_RED);
            return;
        }

        $personType = \Yii::$app->v3toysSettings->shopPersonType;
        $this->stdout("Person: " . $personType->name . "\n");


        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'name'])->one();
        if ($property)
        {
            $this->stdout("\t name — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Имя';
            $property->code             = 'name';
            $property->is_buyer_name    = 'Y';
            $property->is_user_name     = 'Y';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t name — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t name — not added {$errors}\n", Console::FG_RED);
            }
        }


        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'phone'])->one();
        if ($property)
        {
            $this->stdout("\t phone — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Телефон';
            $property->code             = 'phone';
            $property->is_user_phone    = 'Y';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t phone — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t phone — not added {$errors}\n", Console::FG_RED);
            }
        }

        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'email'])->one();
        if ($property)
        {
            $this->stdout("\t email — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Email';
            $property->code             = 'email';
            $property->is_user_email    = 'Y';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t email — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t email — not added {$errors}\n", Console::FG_RED);
            }
        }

        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'shipping_method'])->one();
        if ($property)
        {
            $this->stdout("\t shipping_method — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Доставка';
            $property->code             = 'shipping_method';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t shipping_method — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t shipping_method — not added {$errors}\n", Console::FG_RED);
            }
        }


        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'comment'])->one();
        if ($property)
        {
            $this->stdout("\t comment — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Комментарий';
            $property->code             = 'comment';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t comment — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t comment — not added {$errors}\n", Console::FG_RED);
            }
        }

        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'courier_city'])->one();
        if ($property)
        {
            $this->stdout("\t courier_city — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Город (курьерская доставка)';
            $property->code             = 'courier_city';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t courier_city — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t courier_city — not added {$errors}\n", Console::FG_RED);
            }
        }


        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'courier_address'])->one();
        if ($property)
        {
            $this->stdout("\t courier_address — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Адрес (курьерская доставка)';
            $property->code             = 'courier_address';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t courier_address — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t courier_address — not added {$errors}\n", Console::FG_RED);
            }
        }


        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'pickup_city'])->one();
        if ($property)
        {
            $this->stdout("\t pickup_city — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Город (самовывоз)';
            $property->code             = 'pickup_city';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t pickup_city — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t pickup_city — not added {$errors}\n", Console::FG_RED);
            }
        }

        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'pickup_point_id'])->one();
        if ($property)
        {
            $this->stdout("\t pickup_point_id — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Пункт самовывоза (самовывоз)';
            $property->code             = 'pickup_point_id';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t pickup_point_id — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t pickup_point_id — not added {$errors}\n", Console::FG_RED);
            }
        }


        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'post_index'])->one();
        if ($property)
        {
            $this->stdout("\t post_index — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Индекс (почта)';
            $property->code             = 'post_index';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t post_index — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t post_index — not added {$errors}\n", Console::FG_RED);
            }
        }


        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'post_region'])->one();
        if ($property)
        {
            $this->stdout("\t post_region — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Регион (почта)';
            $property->code             = 'post_region';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t post_region — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t post_region — not added {$errors}\n", Console::FG_RED);
            }
        }

        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'post_area'])->one();
        if ($property)
        {
            $this->stdout("\t post_area — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Область (почта)';
            $property->code             = 'post_area';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t post_area — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t post_area — not added {$errors}\n", Console::FG_RED);
            }
        }

        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'post_city'])->one();
        if ($property)
        {
            $this->stdout("\t post_city — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Город (почта)';
            $property->code             = 'post_city';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t post_city — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t post_city — not added {$errors}\n", Console::FG_RED);
            }
        }

        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'post_address'])->one();
        if ($property)
        {
            $this->stdout("\t post_address — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'Адрес (почта)';
            $property->code             = 'post_address';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t post_address — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t post_address — not added {$errors}\n", Console::FG_RED);
            }
        }

        /**
         * @var $property ShopPersonTypeProperty
         */
        $property = $personType->getShopPersonTypeProperties()->andWhere(['code' => 'post_recipient'])->one();
        if ($property)
        {
            $this->stdout("\t post_recipient — exist \n", Console::FG_YELLOW);
        } else
        {
            $property = new ShopPersonTypeProperty();
            $property->name             = 'полное ФИО получателя (почта)';
            $property->code             = 'post_recipient';
            $property->shop_person_type_id = $personType->id;
            $property->component = PropertyTypeText::className();
            if ($property->save())
            {
                $this->stdout("\t post_recipient — added \n", Console::FG_GREEN);
            } else
            {
                $errors = Json::encode($property->getFirstErrors());
                $this->stdout("\t post_recipient — not added {$errors}\n", Console::FG_RED);
            }
        }

    }

}
