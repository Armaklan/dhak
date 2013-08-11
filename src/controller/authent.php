<?php
/**
 * Authentification page Controller
 *
 * @package authent
 * @author ZUBER Lionel <lionel.zuber@armaklan.org>
 * @version 0.1
 * @copyright (C) 2013 ZUBER Lionel <lionel.zuber@armaklan.org>
 * @license BSD
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

$authentController = $app['controllers_factory'];

$authentController->get('/', function() use ($app) {
    return $app->render('home.html.twig', []);
})->bind("homepage")->before($mustBeLogged);

$authentController->get('/login/{url}', function($url) use($app) {
    return $app->render('login.html.twig', ['error' => "", 'url' => $url]);
})->bind("login");

$authentController->post('/login', function(Request $request) use($app) {

	$login = $request->get('login');
	$password = $request->get('pass');
	$url = $request->get('url');

    try {
        $app["userService"]->login($login, $password);
    } catch (Exception $e) {
        $app["session"]->getFlashBag()->add('error', $e->getMessage());
        return $app->render('login.html.twig', ['url' => $url]);
    }
	$finalUrl = str_replace('!', '/', $url);
	if ($finalUrl == "/") {
		$finalUrl = $app->path('homepage');
	}
    return $app->redirect($finalUrl);

})->bind("login_save");

$authentController->get('/logout', function(Request $request) use($app) {
    $app["userService"]->logout();
    return $app->redirect($app->path('homepage'));
})->bind("logout");


$app->mount('/', $authentController);

?>
