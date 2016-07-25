<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */
/* @var $this yii\web\View */
/* @var $searchModel \skeeks\cms\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<? $pjax = \yii\widgets\Pjax::begin(); ?>

    <?php /*echo $this->render('@skeeks/cms/views/admin-cms-content-element/_search', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'content_id' => $content_id,
        'cmsContent' => $cmsContent,
    ]); */?>

    <?= \skeeks\cms\modules\admin\widgets\GridViewStandart::widget([
        'dataProvider'      => $dataProvider,
        'filterModel'       => $searchModel,
        'autoColumns'       => false,
        'pjax'              => $pjax,
        'adminController'   => $controller,
        'columns'           => [
            'id',
            [
                'class' => \skeeks\cms\grid\CreatedAtColumn::className(),
            ],
            [
                'attribute' => 'user_id',
                'class' => \skeeks\cms\grid\UserColumnData::className(),
            ],
            'name',
            'phone',
            'email',
            [
                'attribute' => 'shipping_method',
                'filter' => \v3toys\skeeks\models\V3toysOrder::getShippingMethods(),
                'value' => function(\v3toys\skeeks\models\V3toysOrder $v3toysOrder)
                {
                    return $v3toysOrder->deliveryName;
                }
            ],
            'v3toys_order_id',

            [
                'attribute' => 'v3toys_status_id',
                'filter' => \yii\helpers\ArrayHelper::map(
                    \v3toys\skeeks\models\V3toysOrderStatus::find()->all(),
                    'v3toys_id', 'name'
                ),
                'value' => function(\v3toys\skeeks\models\V3toysOrder $v3toysOrder)
                {
                    return $v3toysOrder->v3toys_status_id ? $v3toysOrder->status->name : null;
                }
            ],

            [
                'label' => 'К оплате',
                'value' => function(\v3toys\skeeks\models\V3toysOrder $v3toysOrder)
                {
                    return \Yii::$app->money->convertAndFormat($v3toysOrder->money);
                }
            ],
            [
                'label' => 'Скидка',
                'value' => function(\v3toys\skeeks\models\V3toysOrder $v3toysOrder)
                {
                    return \Yii::$app->money->convertAndFormat($v3toysOrder->moneyDiscount);
                }
            ],

            [
                'label' => 'Доставка',
                'value' => function(\v3toys\skeeks\models\V3toysOrder $v3toysOrder)
                {
                    return \Yii::$app->money->convertAndFormat($v3toysOrder->moneyDelivery);
                }
            ],
        ]
    ]); ?>

<? $pjax::end() ?>


