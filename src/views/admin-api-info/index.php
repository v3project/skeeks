<?
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
/* @var $this yii\web\View */
?>
<?= \yii\helpers\Html::a('Документация: http://www.v3toys.ru/index.php?nid=api',
    'http://www.v3toys.ru/index.php?nid=api', [
        'target' => '_blank'
    ]) ?>
<?= \yii\widgets\DetailView::widget([
    'model' => \Yii::$app->v3toysApi,
    'attributes' =>
        [
            'url',
            'version',
            'affiliate_key',
        ]
]) ?>