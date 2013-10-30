<?php
/**
 * Silex app creation
 *
 * @package app
 * @author ZUBER Lionel <lionel.zuber@armaklan.org>
 * @version 0.1
 * @copyright (C) 2013 ZUBER Lionel <lionel.zuber@armaklan.org>
 * @license MIT
 */

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class MyApplication extends Silex\Application
{
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
}

$app = new MyApplication();

$app->after(function( Request $request, Response $response){
	$response->headers->set('Content-type', 'text/json');
});


$app->error(function (\Exception $e, $code){
	switch ($code) {
		case 400:
			$message = 'Bad request.';
			break;
		case 404:
			$message = 'Service not found.';
			break;
		default:
			$message = 'Internal Server Error.';
	};
	return new Response($message, $code);
});