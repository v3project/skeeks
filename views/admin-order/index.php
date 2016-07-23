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
            'name',
            'phone',
            'email',
            'shipping_method',
            'shop_order_id',
            'v3toys_order_id',
            [
                'value' => function(\v3toys\skeeks\models\V3toysOrder $v3toysOrder)
                {
                    return $v3toysOrder->status->title;
                }
            ]
        ]
    ]); ?>

<? $pjax::end() ?>

<? \yii\bootstrap\Alert::begin([
    'options' => [
        'class' => 'alert-info',
    ],
]); ?>

