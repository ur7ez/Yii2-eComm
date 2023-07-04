<?php
require_once __DIR__ . '/../../common/helpers.php';

return [
    'language' => 'uk-UA',
    'sourceLanguage' => 'en-US',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
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
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
                '*' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                ],
            ]
        ],
    ],
];