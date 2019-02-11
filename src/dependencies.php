<?php

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['db'] = function ($container) {
	$dbh = new \App\Config\Dbh();
	return $dbh->connect($container);
};

$container['upload_directory'] = $_SERVER['DOCUMENT_ROOT'].'/matcha/app/users_photos/';

$container['view'] = function ($container) {
	$view = new \Slim\Views\Twig(__DIR__.'/../resources/views', [
		'cache' => false,
	]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

	return $view;
};

$container['fake'] = function($container) {
	return new \App\Models\Fake($container);
};

$container['blocked'] = function($container) {
	return new \App\Models\Blocked($container);
};

$container['people'] = function($container) {
	return new \App\Config\People($container);
};

$container['search'] = function($container) {
	return new \App\Models\Search($container->db);
};
$container['BasicSearch'] = function($container) {
	return new \App\Models\BasicSearch($container->db);
};

$container['chat_messages'] = function($container) {
	return new \App\Models\ChatMessages($container);
};

$container['chats'] = function($container) {
	return new \App\Models\chats($container);
};

$container['notification'] = function($container) {
	return new \App\Models\Notification($container);
};

$container['user'] = function($container) {
	return new \App\Models\User($container);
};

$container['userProfile'] = function($container) {
	return new \App\Models\UserProfile($container->db);
};

$container['visits'] = function($container) {
	return new \App\Models\Visits($container->db);
};

$container['likes'] = function($container) {
	return new \App\Models\Likes($container);
};

$container['tag'] = function($container) {
	return new \App\Models\Tag($container);
};

$container['fame_rating'] = function($container) {
	return new \App\Models\FameRating($container);
};

$container['ChatsController'] = function($container) {
	return new \App\Controllers\User\ChatsController($container);
};

$container['BrowsingHistoryController'] = function($container) {
	return new \App\Controllers\User\BrowsingHistoryController($container);
};

$container['validator'] = function($container) {
	return new \App\Validation\Validator($container);
};

$container['TestController'] = function($container) {
	return new \App\Controllers\TestController($container);
};

$container['HomeController'] = function($container) {
	return new \App\Controllers\HomeController($container);
};

$container['AuthController'] = function($container) {
	return new \App\Controllers\Auth\AuthController($container);
};

$container['ConfirmEmailController'] = function($container) {
	return new \App\Controllers\Auth\ConfirmEmailController($container);
};

$container['PasswordController'] = function($container) {
	return new \App\Controllers\Auth\PasswordController($container);
};

$container['UserProfileController'] = function($container) {
	return new \App\Controllers\User\UserProfileController($container);
};

$container['LikesController'] = function($container) {
	return new \App\Controllers\User\LikesController($container);
};

$container['auth'] = function($container) {
	return new \App\Auth\Auth($container);
};

$container['flash'] = function($container) {
	return new \Slim\Flash\Messages;
};

$container['sendEmail'] = function($container) {
	return new \App\Email\SendEmail;
};

$container['photos'] = function($container) {
	return new \App\Profile\Photos($container);
};

$container['geo'] = function() {
	return new \App\Config\Geo();
};

$container['GeoController'] = function($container) {
	return new \App\Controllers\GeoController($container);
};

$container['NotificationController'] = function($container) {
	return new \App\Controllers\User\NotificationController($container);
};

$container['SearchController'] = function($container) {
	return new \App\Controllers\User\SearchController($container);
};
$container['BasicsearchController'] = function($container) {
	return new \App\Controllers\User\BasicsearchController($container);
};

$container['ShowProfileController'] = function($container) {
	return new \App\Controllers\User\ShowProfileController($container);
};

$container['TagsController'] = function($container) {
	return new \App\Controllers\User\TagsController($container);
};

