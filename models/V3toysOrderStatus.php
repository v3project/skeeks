<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.07.2016
 */
namespace v3toys\skeeks\models;

use Yii;
use yii\base\Model;

/**
 * Class V3toysOrder
 * @package v3toys\skeeks\models
 */
class V3toysOrderStatus extends Model
{
    /**
     * @var int id в системе v3toys
     */
    public $v3toys_id;

    /**
     * @var string Русское название
     */
    public $title;
}
