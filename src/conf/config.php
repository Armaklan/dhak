<?php
/**
 * Development environment configuration parameters
 *
 * @package config
 * @author ZUBER Lionel <lionel.zuber@armaklan.org>
 * @version 0.1
 * @copyright (C) 2013 ZUBER Lionel <lionel.zuber@armaklan.org>
 * @license BSD
 */

$app["debug"] = true;

// Base de donnée
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'jdRoll',
            'user'      => 'root',
            'password'  => '',
            'charset'   => 'utf8',
    ),
));

// Session
$app->register(new Silex\Provider\SessionServiceProvider(array('cookie_lifetime' => 0, 'name' => "_SCOUT_SESS", 'gc_maxlifetime' => 432000)));
$app['session.db_options'] = array(
		'db_table'      => 'session',
		'db_id_col'     => 'session_id',
		'db_data_col'   => 'session_value',
		'db_time_col'   => 'session_time',
);
$app['session.storage.handler'] = $app->share(function () use ($app) {
	return new PdoSessionHandler(
			$app['db']->getWrappedConnection(),
			$app['session.db_options'],
			$app['session.storage.options']
	);
});

// Mailer
$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array());
$app['mailer'] = \Swift_Mailer::newInstance(\Swift_MailTransport::newInstance());

// Logger
$app->register(new Silex\Provider\MonologServiceProvider(), array(
		'monolog.logfile' => __DIR__.'/development.log',
));

?>
