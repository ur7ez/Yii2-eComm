<?php

function isGuest(): bool
{
    return Yii::$app->user->isGuest;
}

function currUserId(): ?int
{
    return Yii::$app->user->id;
}

function param(string $key)
{
    return Yii::$app->params[$key];
}