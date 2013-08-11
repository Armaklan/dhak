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

$app['userService'] = function ($app) {
	return new UserService($app['db'], $app['session'], $app['monolog']);
};

?>
