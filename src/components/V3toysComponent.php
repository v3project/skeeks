<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\components;

use v3toys\skeeks\models\V3toysProductContentElement;
use yii\base\Component;

/**
 * Class V3toysComponent
 *
 * @package v3toys\skeeks\components
 */
class V3toysComponent extends Component
{
    /**
     * @param $cmsContentElement
     *
     * @return int
     */
    /*public function getV3toysIdByCmsElement($cmsContentElement)
    {
        return (int) $cmsContentElement->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName);
    }
    */
    /**
     * @param $cmsContentElement
     *
     * @return int
     */
    public function getV3toysIdByCmsElement($cmsContentElement)
    {
        /**
         * @var $element V3toysProductContentElement
         */
        $element = V3toysProductContentElement::findOne($cmsContentElement->id);
        return (int) $element->v3toysProductProperty->v3toys_id;
    }
}
