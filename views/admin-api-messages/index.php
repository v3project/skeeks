<?
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
/* @var $this yii\web\View */
$response = \Yii::$app->v3toysApi->send('getMessageIdsByPeriod');
?>
<p>2.2.1 Метод getMessageIdsByPeriod - получение списка номеров заявок за период времени</p>

<? if ($response->isError) : ?>
    <? echo $response->error_code; ?>
    <? echo $response->error_message; ?>
<? else : ?>
    <? print_r($response->data); ?>
<? endif; ?>
