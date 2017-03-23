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
]);
$filter->addRule('id', 'integer');
$filter->addRule('q', 'string');

$filter->load(\Yii::$app->request->get());

if ($filter->id)
{
    $dataProvider->query->andWhere(['id' => $filter->id]);
}
/*if ($filter->q)
{
    $dataProvider->query->andWhere(['like', 'keywords', $filter->q]);
}*/
?>
<? $form = \skeeks\cms\modules\admin\widgets\filters\AdminFiltersForm::begin([
        'action' => '/' . \Yii::$app->request->pathInfo,
    ]); ?>

    <?= $form->field($filter, 'id')->label('ID (v3)')->setVisible(); ?>
    <?/*= $form->field($filter, 'q')->label('Поиск')->setVisible(); */?>

<? $form::end(); ?>
