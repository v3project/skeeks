<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 15.04.2016
 */
return
[
    'v3toys' =>
    [
        'priority'  => 400,
        'label'     => \Yii::t('v3toys/skeeks', 'v3toys'),
        "img"       => ['v3toys\skeeks\assets\V3toysAsset', 'icons/logo.png'],

        'items' =>
        [
            [
                "label"     => \Yii::t('skeeks/marketplace', "Catalog"),
                "url"       => ["v3toys/admin-marketplace/catalog"],
                "img"       => ['v3toys\skeeks\assets\V3toysAsset', 'icons/logo.png'],
            ],
        ]
    ],
];