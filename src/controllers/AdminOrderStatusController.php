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
use skeeks\cms\models\Comment;
use v3toys\skeeks\models\V3toysOrderStatus;
use yii\helpers\ArrayHelper;

/**
 * Class AdminOrderStatusController
 *
 * @package v3toys\skeeks\controllers
 */
class AdminOrderStatusController extends BackendModelStandartController
{
    public function init()
    {
        $this->name = \Yii::t('v3toys/skeeks', 'Статусы заказов');
        $this->modelShowAttribute = "id";
        $this->modelClassName = V3toysOrderStatus::class;

        parent::init();
    }


    public function actions()
    {
        $actions = ArrayHelper::merge(parent::actions(), [
            'index' => [

                'filters' => false,

                "grid" => [
                    'defaultOrder'   => [
                        'v3toys_id' => SORT_DESC,
                    ],
                    'visibleColumns' => [
                        'v3toys_id',
                        'name',
                    ],

                ],
            ],
        ]);

        ArrayHelper::remove($actions, 'delete');
        ArrayHelper::remove($actions, 'delete-multi');
        ArrayHelper::remove($actions, 'update');
        ArrayHelper::remove($actions, 'create');
        return $actions;
    }
}
