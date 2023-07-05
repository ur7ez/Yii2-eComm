<?php

namespace common\components;

use yii\i18n\MissingTranslationEvent;

/**
 * @package common\components
 */
class TranslationEventHandler
{
    public static function handleMissingTranslation(MissingTranslationEvent $event)
    {
        $event->translatedMessage = sprintf('[[%s]]',
            implode('-', [$event->message, $event->category, $event->language])
        );
    }
}