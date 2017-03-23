<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
namespace v3toys\skeeks\components;

use v3toys\skeeks\models\V3toysProductContentElement;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\web\Application;

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
        }
    }
}
