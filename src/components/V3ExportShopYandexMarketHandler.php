<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 29.08.2016
 */

namespace v3toys\skeeks\components;

use skeeks\cms\export\ExportHandlerFilePath;
use skeeks\cms\exportShopYandexMarket\ExportShopYandexMarketHandler;
use skeeks\cms\importCsv\handlers\CsvHandler;
use skeeks\cms\importCsvContent\widgets\MatchingInput;
use skeeks\cms\models\CmsContent;
use skeeks\cms\shop\models\ShopCmsContentElement;
use v3toys\skeeks\models\V3toysProductProperty;
use yii\widgets\ActiveForm;

/**
 * @property CmsContent $cmsContent
 *
 * Class CsvContentHandler
 *
 * @package skeeks\cms\importCsvContent
 */
class V3ExportShopYandexMarketHandler extends ExportShopYandexMarketHandler
{
    public function init()
    {
        parent::init();
        $this->name = '[Xml] Экспорт V3 товаров в yandex.market';
    }

    /**
     * @param ActiveForm $form
     */
    public function renderConfigForm(ActiveForm $form)
    {
        parent::renderConfigForm($form);

    }

    protected function _initOffer($xoffers, ShopCmsContentElement $element)
    {

        $v3Property = V3toysProductProperty::findOne($element->id);

        $xoffer = parent::_initOffer($xoffers, $element);

        if (!$this->vendor) {
            $xoffer->appendChild(new \DOMElement('vendor', $v3Property->v3toys_brand_name));
        }

        if (!$this->vendor_code) {
            $xoffer->appendChild(new \DOMElement('vendorCode', $v3Property->sku));
        }

        return $xoffer;
    }
}