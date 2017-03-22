<?
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
/* @var $this yii\web\View */
$query = (new \yii\db\Query())
            ->from('apiv5.affproduct')
;
\skeeks\cms\models\CmsContentElement::find()
$count = $query->count("*", \Yii::$app->dbV3project);
?>

<?= \skeeks\cms\modules\admin\widgets\GridViewStandart::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $query->
    ]),
    'columns' =>
    [
        'id',
        'title',
        'deleted',
        'quantity',
        'buy_price',
        'base_price',
        'mr_price',
        'price',
        'excl_quantity',
        'sku',
        'barcode',
        'brand',
    ]
]); ?>
