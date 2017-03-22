<?php
use skeeks\cms\mail\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \v3toys\skeeks\models\V3toysOrder */
$url = \yii\helpers\Url::to(['/shop/order/view', 'id' => $model->id], true);
?>

<?= Html::beginTag('h1'); ?>
    <?= \Yii::t('skeeks/shop/app', 'New order'); ?> #<?= $model->id; ?> <?= \Yii::t('skeeks/shop/app', 'in site'); ?> <?= \Yii::$app->cms->appName ?>
<?= Html::endTag('h1'); ?>



<hr />
<?= Html::beginTag('h2'); ?>
    Заказ:
<?= Html::endTag('h2'); ?>
<?= Html::beginTag('p'); ?>
    <?=
        \yii\grid\GridView::widget([
            'dataProvider'    => new \yii\data\ArrayDataProvider([
                'allModels' => $model->baskets
            ]),
            'layout' => "{items}",
            'columns'   =>
            [
                /*[
                    'class' => \yii\grid\SerialColumn::className()
                ],*/

                [
                    'class'     => \yii\grid\DataColumn::className(),
                    'format'    => 'raw',
                    'value'     => function(\v3toys\skeeks\models\V3toysOrderBasket $shopBasket)
                    {
                        if ($shopBasket->image)
                        {
                            return Html::img($shopBasket->image->absoluteSrc, ['width' => 80]);
                        }
                    }
                ],
                [
                    'class' => \yii\grid\DataColumn::className(),
                    'attribute' => 'name',
                    'label' => 'Название',
                    'format' => 'raw',
                    'value' => function(\v3toys\skeeks\models\V3toysOrderBasket $shopBasket)
                    {
                        if ($shopBasket->url)
                        {
                            return Html::a($shopBasket->name, $shopBasket->absoluteUrl, [
                                'target' => '_blank',
                                'titla' => "Смотреть на сайте",
                                'data-pjax' => 0
                            ]);
                        } else
                        {
                            return $shopBasket->name;
                        }

                    }
                ],

                [
                    'class' => \yii\grid\DataColumn::className(),
                    'label' => 'Количество',
                    'attribute' => 'quantity',
                    'value' => function(\v3toys\skeeks\models\V3toysOrderBasket $shopBasket)
                    {
                        return $shopBasket->quantity . " шт.";
                    }
                ],

                [
                    'class' => \yii\grid\DataColumn::className(),
                    'label' => \Yii::t('skeeks/shop/app', 'Price'),
                    'attribute' => 'price',
                    'format' => 'raw',
                    'value' => function(\v3toys\skeeks\models\V3toysOrderBasket $shopBasket)
                    {

                        return \Yii::$app->money->intlFormatter()->format($shopBasket->money);

                    }
                ],
                [
                    'class' => \yii\grid\DataColumn::className(),
                    'label' => \Yii::t('skeeks/shop/app', 'Sum'),
                    'attribute' => 'price',
                    'format' => 'raw',
                    'value' => function(\v3toys\skeeks\models\V3toysOrderBasket $shopBasket)
                    {
                        return \Yii::$app->money->intlFormatter()->format($shopBasket->money->multiply($shopBasket->quantity));
                    }
                ],
            ]
        ])
    ?>
<?= Html::endTag('p'); ?>

<?= Html::beginTag('h2'); ?>
    Итого:
<?= Html::endTag('h2'); ?>
<?= Html::beginTag('p'); ?>
    Стоимость товаров: <b><?= Html::tag('b', \Yii::$app->money->intlFormatter()->format($model->moneyOriginal)); ?></b><br />
    Стоимость доставки: <b><?= Html::tag('b', \Yii::$app->money->intlFormatter()->format($model->moneyDelivery)); ?></b><br />
    К оплате: <b><?= Html::tag('b', \Yii::$app->money->intlFormatter()->format($model->money)); ?></b>
<?= Html::endTag('p'); ?>

<?= Html::beginTag('h2'); ?>
    Данные покупателя:
<?= Html::endTag('h2'); ?>
<?=
    \yii\widgets\DetailView::widget([
        'model'         => $model,
        'attributes'    =>
        [
            'name',
            'email',
            'phone',
            [
                'label' => 'Адрес',
                'value' => $model->dadataAddress ? $model->dadataAddress->unrestrictedValue : ''
            ],
            [
                'label' => 'Доставка',
                'value' => $model->deliveryFullName
            ]
        ]
    ]);
?>

<?= Html::beginTag('p'); ?><!--
    <?/*= \Yii::t('skeeks/shop/app', 'The details of the order, you can track on the page'); */?>: --><?/*= Html::a($model->u, $url); */?>
<?= Html::endTag('p'); ?>