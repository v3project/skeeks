<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */

namespace v3toys\skeeks\controllers;

use skeeks\cms\backend\controllers\BackendModelStandartController;
use skeeks\cms\components\marketplace\models\PackageModel;
use skeeks\cms\grid\DateTimeColumnData;
use skeeks\cms\models\Comment;
use v3toys\skeeks\models\V3toysOrder;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class AdminOrderController
 * @package v3toys\skeeks\controllers
 */
class AdminOrderController extends BackendModelStandartController
{
    public function init()
    {
        $this->name = \Yii::t('v3toys/skeeks', 'Заказы');
        $this->modelShowAttribute = "id";
        $this->modelClassName = V3toysOrder::class;

        parent::init();
    }

    public function actions()
    {
        $actions = ArrayHelper::merge(parent::actions(), [
            'index' => [

                'filters' => [
                    'visibleFilters' => [
                        'email',
                        'name',
                    ],
                ],

                "grid" => [
                    'defaultOrder'   => [
                        'created_at' => SORT_DESC,
                    ],
                    'visibleColumns' => [
                        'checkbox',
                        'actions',

                        'id',
                        'created_at',
                        'user_id',

                        'name',
                        'phone',
                        'email',

                        'shipping_method',
                        'v3toys_order_id',
                        'v3toys_status_id',

                        'money',
                    ],
                    'columns'        => [
                        'created_at' => [
                            'class' => DateTimeColumnData::class
                        ],
                        'money' => [
                            'label' => 'К оплате',
                            'value' => function (V3toysOrder $model) {
                                return $model->money;
                            },
                        ],
                        'id' => [
                            'value' => function (V3toysOrder $model) {
                                return Html::a($model->id, Url::to(['/v3toys/cart/finish', 'key' => $model->key]), [
                                    'data-pjax' => 0,
                                    'target' => "_blank"
                                ]);
                            },
                        ],
                        'v3toys_status_id' => [
                            'value' => function (V3toysOrder $model) {
                                return $model->v3toys_status_id ? $model->status->name : null;
                            },
                        ],
                        'shipping_method' => [
                            'value' => function (V3toysOrder $model) {
                                return $model->deliveryName;
                            },
                        ],
                    ],
                ],
            ],
        ]);

        ArrayHelper::remove($actions, 'create');
        return $actions;
    }
}
