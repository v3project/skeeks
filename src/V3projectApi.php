<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks;
use v3toys\v3project\api\Api;

/**
 * Actual v3toys api
 * Ключ аффилиаата устанавливается из настроек проекта
 * Можно обойтись и без этого класса, но тогда нужно ставить какой то компонент или модуль в bootstrap, чего пока делать не хочется
 *
 * Class V3toysApi
 *
 * @package v3toys\skeeks
 */
class V3projectApi extends Api
{
    public function init()
    {
        parent::init();

        //affiliate key из настроек
        if (\Yii::$app->v3toysSettings->affiliate_key)
        {
            $this->affiliate_key = \Yii::$app->v3toysSettings->affiliate_key;
        }
    }
}