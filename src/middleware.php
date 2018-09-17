<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\AuthMiddleware($container));
$app->add(new \App\Middleware\FlashMiddleware($container));
$app->add(new \App\Middleware\UserProfileMiddleware($container));
$app->add(new \App\Middleware\AuthMiddleware($container));
$app->add(new \App\Middleware\ActivityMiddleware($container));
$app->add(new \App\Middleware\VisitNotificationMiddleware($container));