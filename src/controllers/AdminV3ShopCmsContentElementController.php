<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */

namespace v3toys\skeeks\controllers;

use skeeks\cms\backend\actions\BackendModelAction;
use skeeks\cms\components\marketplace\models\PackageModel;
use skeeks\cms\models\Comment;
use skeeks\cms\shop\controllers\AdminCmsContentElementController;
use skeeks\sx\helpers\ResponseHelper;
use v3toys\skeeks\models\V3toysProductContentElement;
use v3toys\skeeks\models\V3toysProductProperty;
use v3toys\skeeks\models\V3toysShippingCity;

/**
 * Class AdminV3ShopCmsContentElementController
 * @package v3toys\skeeks\controllers
 */
class AdminV3ShopCmsContentElementController extends AdminCmsContentElementController
{
    public $modelClassName = V3toysProductContentElement::class;

    public function actions()
    {
        $actions = parent::actions();

        if ($this->content && \Yii::$app->v3toysSettings->content_ids && in_array($this->content->id, (array)\Yii::$app->v3toysSettings->content_ids)) {
            $actions['v3project'] = [
                'class'    => BackendModelAction::class,
                'name'     => 'Сязь с V3project',
                "icon"     => "fa fa-child",
                "priority" => 500,
                "callback" => [$this, 'actionV3project'],
            ];
        }

        return $actions;
    }

    public function actionV3project()
    {
        $rr = new ResponseHelper();
        /**
         * @var $model V3toysProductContentElement
         */
        $model = $this->model;
        $property = $model->v3toysProductProperty;

        if (!$property) {
            $property = new V3toysProductProperty();
            $property->id = $model->id;
        }

        if (\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax) {
            return $rr->ajaxValidateForm($property);
        }

        if ($rr->isRequestAjaxPost) {
            if ($property->load(\Yii::$app->request->post()) && $property->save()) {
                \Yii::$app->getSession()->setFlash('success', \Yii::t('skeeks/cms', 'Saved'));

                if (\Yii::$app->request->post('submit-btn') == 'apply') {
                } else {
                    return $this->redirect(
                        $this->url
                    );
                }

                $model->refresh();
            }
        }

        return $this->render('@v3toys/skeeks/views/admin-cms-content-element/v3project', [
            'property' => $property,
        ]);
    }

}
