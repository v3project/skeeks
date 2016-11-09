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
<? $pjax = \skeeks\cms\modules\admin\widgets\Pjax::begin(); ?>

    <?php echo $this->render('_search', [
        'searchModel'   => $searchModel,
        'dataProvider'  => $dataProvider
    ]); ?>

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

    <? if ($dataProvider->query->count()) : ?>
        <?
    /**
     * @var $order \v3toys\skeeks\models\V3toysOrder $order
     */
        $moneyTotalNoDelivery = \Yii::$app->money->newMoney();
        $moneyTotal = \Yii::$app->money->newMoney();

        $purchasingPrice = 0;
        $notFoundProducts = 0;
        ?>
        <? foreach ($dataProvider->query->all() as $order) : ?>

            <?
                if ($order->products)
                {


                    foreach ($order->products as $productData)
                    {
                        $v3toysId   = \yii\helpers\ArrayHelper::getValue($productData, 'v3toys_product_id');
                        $id         = \yii\helpers\ArrayHelper::getValue($productData, 'product_id');
                        $quantity   = \yii\helpers\ArrayHelper::getValue($productData, 'quantity');
                        $price   = \yii\helpers\ArrayHelper::getValue($productData, 'price');


                        /**
                         * @var $element \skeeks\cms\shop\models\ShopCmsContentElement
                         */
                        if ($element = \skeeks\cms\shop\models\ShopCmsContentElement::findOne((int) $id))
                        {
                            $purchasingPrice = $purchasingPrice + $quantity * (float) $element->shopProduct->purchasing_price;
                        } else
                        {
                            $notFoundProducts ++;
                        }
                    }
                }
            ?>

            <?
                $moneyPurchasing = \Yii::$app->money->newMoney((string) $purchasingPrice);
                $moneyTotal = $moneyTotal->add($order->money);
                $moneyTotalNoDelivery = $moneyTotalNoDelivery->add($order->moneyOriginal);
            ?>
        <? endforeach; ?>

        <hr />
        <p>Всего куплено товаров: <b><?= \Yii::$app->money->convertAndFormat($moneyTotal); ?></b></p>
        <p>Всего куплено товаров (без учета доставки): <b><?= \Yii::$app->money->convertAndFormat($moneyTotalNoDelivery); ?></b></p>
        <p>Всего куплено товаров (закупка): <b><?= \Yii::$app->money->convertAndFormat($moneyPurchasing); ?></b></p>
        <p>Закупка - Реальная цена продажи: <b><?= \Yii::$app->money->convertAndFormat($moneyTotalNoDelivery->subtract($moneyPurchasing)); ?></b></p>
        <p>Минус коммисия v3tosy.ru (50%): <b style="font-size: 20px;"><?= \Yii::$app->money->convertAndFormat($moneyTotalNoDelivery->subtract($moneyPurchasing)->multiply(0.5)); ?></b> <small>грязный доход аффилиата</small></p>
        <hr />
        <? if ($notFoundProducts) : ?>
            Не найдены некоторые товары: <?= $notFoundProducts; ?> <small>(Влияет на стоимость закупки)</small>
        <? endif; ?>
    <? endif; ?>

<? $pjax::end() ?>


