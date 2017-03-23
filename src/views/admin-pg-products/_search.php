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
    'own',
    'available',
]);
$filter->addRule('available', 'integer');
$filter->addRule('id', 'integer');
$filter->addRule('q', 'string');
$filter->addRule('own', 'string');

$filter->load(\Yii::$app->request->get());

if ($filter->id)
{
    $dataProvider->query->andWhere(['id' => $filter->id]);
}
if ($filter->q)
{
    $dataProvider->query->andWhere(['like', 'keywords', $filter->q]);
    //$dataProvider->query->andWhere("keywords::text LIKE '%{$filter->q}%'");
}
if ($filter->available)
{
    //$dataProvider->query->andWhere(['like', 'keywords::TEXT', $filter->q]);
    $dataProvider->query->andWhere("guiding_available_quantity > 0");
}
if ($filter->own)
{
    $ownIds = [];

    $query2 = (new \yii\db\Query())
                ->from('apiv5.affproduct')
                ->indexBy('product_id')
    ;

    if ($filter->own == 'text')
    {
        $query2->andWhere(['description' => null]);
    }

    if ($filter->own == 'prop')
    {
        $query2->andWhere(['title' => null]);
    }

    $affProds = $query2->all(\Yii::$app->dbV3project);
    if ($affProds)
    {
        $ownIds = array_keys($affProds);
    }

    $dataProvider->query->andWhere(['id' => $ownIds]);
}
?>
<? $form = \skeeks\cms\modules\admin\widgets\filters\AdminFiltersForm::begin([
        'action' => '/' . \Yii::$app->request->pathInfo,
    ]); ?>

    <?= $form->field($filter, 'id')->label('ID (v3)')->setVisible(); ?>
    <?= $form->field($filter, 'q')->label('Поиск')->setVisible(); ?>
    <?= $form->field($filter, 'available')->label('Только в наличии')->checkbox(['label' => 'Только в наличии'])->setVisible(); ?>
    <?= $form->field($filter, 'own')->listBox([
        null => ' - ',
        'all' => 'Все мои',
        'text' => 'Мои без описания',
        'prop' => 'Мои без свойств',
    ], ['size' => 1])->label('Только мои')->setVisible(); ?>

<? $form::end(); ?>
