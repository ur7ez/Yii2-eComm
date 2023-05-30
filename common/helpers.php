<?php

function isGuest(): bool
{
    return Yii::$app->user->isGuest;
}

function currUserId(): ?int
{
    return Yii::$app->user->id;
}