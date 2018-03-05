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
    ]); */ ?>

<?= \skeeks\cms\modules\admin\widgets\GridViewStandart::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'autoColumns' => false,
    'pjax' => $pjax,
    'adminController' => $controller,
    'columns' => [
        'id',
        [
            'class' => \skeeks\cms\grid\CreatedAtColumn::className(),
        ],
        [
            'attribute' => 'user_id',
            'class' => \skeeks\cms\grid\UserColumnData::className(),
        ],
        'full_name',
        'phone',
        'email',

        'status_name',

        [
            'label' => 'К оплате',
            'value' => function (\v3toys\skeeks\models\V3toysMessage $v3toysOrder) {
                return \Yii::$app->money->convertAndFormat($v3toysOrder->money);
            }
        ],

    ]
]); ?>

<? $pjax::end() ?>


