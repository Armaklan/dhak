<?php
/**
 * Declare all service need in this app
 *
 * @package service
 * @author ZUBER Lionel <lionel.zuber@armaklan.org>
 * @version 0.1
 * @copyright (C) 2013 ZUBER Lionel <lionel.zuber@armaklan.org>
 * @license MIT
 */

require __DIR__.'/../service/userService.php';
require __DIR__.'/../service/uniteService.php';
require __DIR__.'/../service/campService.php';

$app['userService'] = function ($app) {
	return new UserService($app['db'], $app['session'], $app['monolog']);
};
$app['uniteService'] = function ($app) {
	return new UniteService($app['db'], $app['session'], $app['monolog']);
};
$app['campService'] = function ($app) {
	return new CampService($app['db'], $app['session'], $app['monolog']);
};

?>
