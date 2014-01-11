<?php
/**
 * Main package of this app - include all need files
 *
 * @package index
 * @author ZUBER Lionel <lionel.zuber@armaklan.org>
 * @version 0.1
 * @copyright (C) 2013 ZUBER Lionel <lionel.zuber@armaklan.org>
 * @license MIT
 */

require __DIR__.'/vendor/autoload.php';

require __DIR__.'/src/app/app.php';
require __DIR__.'/src/conf/config.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/src/views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


$mustBeLogged = function (Request $request) use ($app) {
	if (!isLog($app)) {
		return new Response('Unauthorized', 403);
	}
};

function isLog($app) {
	return ($app['session']->get('user') != null);
}


function getCorrectResponse($obj) {
    return new Response(
        json_encode($obj), 
        200, 
        array('Content-Type' => 'application/json')
    );
}

require __DIR__.'/src/app/service.php';
require __DIR__.'/src/app/controller.php';
$app->run();
