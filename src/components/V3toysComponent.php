<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\components;

use skeeks\cms\backend\BackendComponent;
use v3toys\skeeks\models\V3toysProductContentElement;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\Controller;
use yii\web\View;

/**
 * Class V3toysComponent
 *
 * @package v3toys\skeeks\components
 */
class V3toysComponent extends Component
    implements BootstrapInterface
{
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

    /**
     * @param \yii\base\Application $application
     */
    public function bootstrap($application)
    {
        if ($application instanceof Application)
        {
            $application->on(Application::EVENT_BEFORE_REQUEST, function($e)
            {

                //Поисковые запросы К11111
                if (!isset(\Yii::$app->cmsSearch))
                {
                    return false;
                }

                $query = trim(\Yii::$app->cmsSearch->searchQuery);
                $matches = [];
                if (preg_match('/s*[KkКк]\s*(\d+)\s*$/', $query, $matches))
                {
                    if (isset($matches[1]))
                    {
                        $query = \v3toys\skeeks\models\V3toysProductContentElement::find()
                                    ->joinWith('v3toysProductProperty as p')
                                    ->andWhere(['p.v3toys_id' => $matches[1]])
                        ;
                        if ($element = $query->one())
                        {
                            \Yii::$app->response->redirect($element->url);
                            \Yii::$app->end();
                        }
                    }
                }
            });


            Event::on(Controller::class, Controller::EVENT_BEFORE_ACTION, function($e) {
                $this->initDefaultCanUrl();
            });


            $application->on(Application::EVENT_AFTER_REQUEST, function($e)
            {
                if ($this->isTrigerEventCanUrl())
                {
                    \Yii::$app->canurl->event_after_request($e);
                }
            });

            $application->view->on(View::EVENT_END_PAGE, function($e)
            {
                if ($this->isTrigerEventCanUrl())
                {
                    \Yii::$app->canurl->event_end_page($e);
                }
            });
        }
    }

    public function initDefaultCanUrl()
    {
        if (\Yii::$app->requestedRoute)
        {
            $requestedUrl = Url::to(ArrayHelper::merge(["/" . \Yii::$app->requestedRoute], (array) \Yii::$app->request->queryParams));
            $autoPath = ArrayHelper::getValue(parse_url($requestedUrl), 'path');
            \Yii::$app->canurl->path = $autoPath;
        } else if (\Yii::$app->cms->currentTree)
        {
            \Yii::$app->canurl->path = \Yii::$app->cms->currentTree->url;
        }

        \Yii::$app->canurl->SETcore_params([]);
        \Yii::$app->canurl->SETimportant_params([]);

        if (\Yii::$app->controller->action->uniqueId == 'cms/tree/view')
        {
            \Yii::$app->canurl->ADDimportant_params(['per-page' => \Yii::$app->request->get('per-page')]);
            \Yii::$app->canurl->ADDminor_params(['page' => null]);
            \Yii::$app->canurl->ADDimportant_pnames(['ProductFilters']);
            \Yii::$app->canurl->ADDimportant_pnames(['SearchProductsModel']);
            \Yii::$app->canurl->ADDimportant_pnames(['SearchRelatedPropertiesModel']);
        }

        if (\Yii::$app->controller->action->uniqueId == 'v3toys/cart/finish')
        {
            \Yii::$app->canurl->ADDimportant_pnames(['key']);
        }
        
    }


    public function isTrigerEventCanUrl()
    {
        if (BackendComponent::getCurrent())
        {
            return false;
        }

        if (\Yii::$app->controller && \Yii::$app->controller->uniqueId == 'cms/imaging')
        {
            return false;
        }

        return true;
    }
}
