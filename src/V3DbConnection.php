<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 22.03.2017
 */
namespace v3toys\skeeks;
use yii\db\Connection;

/**
 * Class V3DbConnection
 * @package v3toys\skeeks
 */
class V3DbConnection extends Connection
{
    public $dsn         = 'pgsql:host=db.v3project.ru;port=5432;dbname=v3toys_ru';
    public $charset     = 'utf8';

    public function init()
    {
        if (!$this->username)
        {
            $this->username = 'aff_' . \Yii::$app->v3toysSettings->affiliate_key;
        }

        if (!$this->password)
        {
            $this->password = 'aff_' . \Yii::$app->v3toysSettings->affiliate_key;
        }

        parent::init();
    }
}