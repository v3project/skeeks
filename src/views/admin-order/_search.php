<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 21.09.2016
 */

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchs\Game */
/* @var $dataProvider yii\data\ActiveDataProvider */

$filter = new \yii\base\DynamicModel([
    'id',
    'q',
    'v3toys_statuses',
]);
$filter->addRule('id', 'integer');
$filter->addRule('q', 'string');
$filter->addRule('v3toys_statuses', 'safe');

$filter->load(\Yii::$app->request->get());

if ($filter->id) {
    $dataProvider->query->andWhere(['id' => $filter->id]);
}
if ($filter->q) {
    $dataProvider->query->andWhere([
        'or',
        ['like', 'id', $filter->q],
        ['like', 'name', $filter->q],
        ['like', 'email', $filter->q],
        ['like', 'phone', $filter->q],
        ['like', 'v3toys_order_id', $filter->q],
    ]);
}
if ($filter->v3toys_statuses) {
    $dataProvider->query->andWhere([
        'v3toys_status_id' => $filter->v3toys_statuses
    ]);
}
?>
<? $form = \skeeks\cms\modules\admin\widgets\filters\AdminFiltersForm::begin([
    'action' => '/' . \Yii::$app->request->pathInfo,
]); ?>

<?= $form->field($filter, 'q')->label('Поиск')->textInput([
    'placeholder' => 'Поиск по имени, телефону, email, номеру зкаказа'
])->setVisible(); ?>
<?= $form->field($filter, 'id'); ?>
<?= $form->field($searchModel, 'name'); ?>
<?= $form->field($searchModel, 'phone'); ?>
<?= $form->field($searchModel, 'email'); ?>
<?= $form->field($filter, 'v3toys_statuses')->label('Статус')->widget(
    \skeeks\widget\chosen\Chosen::className(),
    [
        'multiple' => true,
        'items' => \yii\helpers\ArrayHelper::map(
            \v3toys\skeeks\models\V3toysOrderStatus::find()->all(), 'v3toys_id', 'name'
        )
    ]
); ?>

<? $form::end(); ?>
