<?
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
/* @var $this yii\web\View */
$response = \Yii::$app->v3toysApi->getStatus();
?>
<p>3.1.5 Метод getStatus - возвращает все возможные статусы заказов в V3Project</p>

<? if ($response->isError) : ?>
    <p>Нет доступа к БД V3Project в рамках V3ProjectAPIv5</p>
    <? echo $response->error_code; ?>
    <? echo $response->error_message; ?>
<? else : ?>
    <?= \skeeks\cms\modules\admin\widgets\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'models' => (array)$response->data
        ]),
        'columns' =>
            [
                'id',
                'title'
            ]
    ]); ?>
<? endif; ?>
