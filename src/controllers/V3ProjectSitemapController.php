<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */
namespace v3toys\skeeks\controllers;

use skeeks\cms\base\Controller;
use skeeks\cms\components\Cms;
use skeeks\cms\filters\CmsAccessControl;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\seo\controllers\SitemapController;
use skeeks\cms\shop\models\ShopBasket;
use skeeks\cms\shop\models\ShopBuyer;
use skeeks\cms\shop\models\ShopFuser;
use skeeks\cms\shop\models\ShopOrder;
use skeeks\cms\shop\models\ShopPersonType;
use skeeks\cms\shop\models\ShopPersonTypeProperty;
use skeeks\cms\shop\models\ShopProduct;
use v3toys\skeeks\models\V3toysOrder;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class V3ProjectSitemapController
 * @package v3toys\skeeks\controllers
 */
class V3ProjectSitemapController extends SitemapController
{
    /**
     * @param array $data
     * @return $this
     */
    protected function _addAdditional(&$data = [])
    {
        /*$data[] = [
            'loc' => Url::to(['/cms/cms/index'], true)
        ];*/

        return $this;
    }
}