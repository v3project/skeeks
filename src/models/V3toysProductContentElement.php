<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 06.03.2016
 */

namespace v3toys\skeeks\models;

use skeeks\cms\models\CmsContentElement;
use skeeks\cms\shop\models\ShopCmsContentElement;

/**
 * @property V3toysProductProperty $v3toysProductProperty
 *
 * Class ShopCmsContentElement
 * @package skeeks\cms\shop\models
 */
class V3toysProductContentElement extends ShopCmsContentElement
{

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getV3toysProductProperty()
    {
        return $this->hasOne(V3toysProductProperty::className(), ['id' => 'id']);
    }


}