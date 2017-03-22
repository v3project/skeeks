<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\controllers;

use skeeks\cms\components\marketplace\models\PackageModel;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\Comment;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\controllers\AdminController;
use Yii;
use skeeks\cms\models\User;
use skeeks\cms\models\searchs\User as UserSearch;

/**
 * Class AdminApiProductsController
 * @package v3toys\skeeks\controllers
 */
class AdminApiProductsController extends AdminController
{
    public function init()
    {
        $this->name                   = \Yii::t('v3toys/skeeks', 'Товары');
        parent::init();
    }

    public function actionIndex()
    {
        return $this->render($this->action->id);
    }
}
