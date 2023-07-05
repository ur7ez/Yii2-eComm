<?php
use common\components\TranslationEventHandler;

require_once __DIR__ . '/../../common/helpers.php';

return [
    'language' => 'en-US', // 'uk-UA'
//    'sourceLanguage' => 'en-US',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'formatter' => [
            'class' => \common\i18n\Formatter::class,
            'datetimeFormat' => 'php:d.m.Y H:i',
            'dateFormat' => 'dd.MM.yyyy',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
//            'currencyCode' => 'USD',
        ],
        'i18n' => [
            'translations' => [
//                'yii' => [
//                    'class' => \yii\i18n\PhpMessageSource::class,
//                    'basePath' => '@common/messages/yii',
//                ],
                'app*' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    // 'class' => \yii\i18n\DbMessageSource::class,
                    'basePath' => '@common/messages',
                    'on missingTranslation' => [
                        TranslationEventHandler::class, 'handleMissingTranslation'
                    ],
                ],
                '*' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                ],
            ],
        ],
    ],
];