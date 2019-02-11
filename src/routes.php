<?php

use Slim\Http\Request;
use Slim\Http\Response;

use App\Middleware\AuthControlMiddleware;
use App\Middleware\GuestControlMiddleware;
use App\Middleware\ActivityMiddleware;
use App\Middleware\VisitNotificationMiddleware;
use App\Middleware\IsFilledMiddleware;

$app->get('/test', 'TestController:index')->setName('test');

$app->get('/', 'HomeController:index')->setName('home');
$app->get('/is_auth', 'AuthController:isAuth');

$app->group('', function() {
	$this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
	$this->post('/auth/signup', 'AuthController:postSignUp');

	$this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
	$this->get('/auth/reinit', 'AuthController:reinit')->setName('auth.reinit');
	$this->post('/auth/signin', 'AuthController:postSignIn');
	$this->post('/auth/reinit', 'AuthController:postreinit');

	$this->get('/auth/email/confirm', 'ConfirmEmailController:getConfirm')->setName('auth.email.confirm');
	$this->get('/auth/password/restore', 'PasswordController:restorePass')->setName('auth.password.restore');
	$this->post('/auth/password/restore', 'PasswordController:postrestorePassword')->setName('auth.password.restore');

})->add(new GuestControlMiddleware($container));

$app->group('', function() use ($container) {
	$this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');
	$this->get('/search', 'SearchController:search')->setName('search');
	$this->post('/search', 'SearchController:filters');
	$this->get('/user/profile', 'UserProfileController:getUserProfile')->setName('user.profile');
	$this->post('/user/profile', 'UserProfileController:postUserProfile');
	$this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
	$this->post('/auth/password/change', 'PasswordController:postChangePassword');

	$this->group('', function() use ($container) {
		$this->get('/user/error', 'UserProfileController:error')->setName('user.error');
		$this->get('/user/blacklist', 'UserProfileController:blacklist')->setName('user.blacklist');
		$this->post('/user/blacklist', 'UserProfileController:blacklistPost');
		$this->post('/user/report_as_fake', 'UserProfileController:fake');

		$this->get('/show', 'ShowProfileController:show')->setName('showProfile');
		$this->post('/show', 'ShowProfileController:postShow');
														
		$this->post('/getCountry', 'GeoController:getCountry')->setName('getCountry');
	
		$this->get('/user/notifications', 'NotificationController:notifications')->setName('user.notifications');
		$this->post('/user/notifications/viewed', 'NotificationController:viewed');
		$this->post('/user/notifications/delete', 'NotificationController:delete');
		$this->post('/user/notifications/count', 'NotificationController:count');
		$this->post('/user/notifications/load', 'NotificationController:postNotifications');
	
		$this->get('/user/browsing_history', 'BrowsingHistoryController:index')->setName('user.browsing_history');
		$this->post('/user/browsing_history', 'BrowsingHistoryController:load');
		$this->get('/user/browsing_history/clear_history', 'BrowsingHistoryController:delete')->setName('user.browsing_history.delete');
	
		$this->get('/chats', 'ChatsController:index')->setName('chats');
		$this->get('/chats/chat/{chanel}', 'ChatsController:chat');
		$this->get('/chats/get_chats', 'ChatsController:get_chats');
		$this->post('/chats/save_message', 'ChatsController:save_message');
		$this->post('/chats/send_message', 'ChatsController:send_message');
		$this->post('/chats/load', 'ChatsController:load_messages');
	
		$this->post('/user/like', 'LikesController:like')->setName('user.like');
	
		$this->post('/user/save_tag', 'TagsController:save_tag');
		$this->post('/user/get_tags', 'TagsController:get_tags');
	})->add(new IsFilledMiddleware($container));
})->add(new AuthControlMiddleware($container))
  ->add(new ActivityMiddleware($container));
