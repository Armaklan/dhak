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

$app['topicService'] = function ($app) {
	return new TopicService($app['db'], $app['session'], $app['monolog']);
};

?>
