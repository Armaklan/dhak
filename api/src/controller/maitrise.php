<?php
/**
 * Gestion de la maitrise des unités.
 *
 * @package authent
 * @author ZUBER Lionel <lionel.zuber@armaklan.org>
 * @version 0.1
 * @copyright (C) 2013 ZUBER Lionel <lionel.zuber@armaklan.org>
 * @license BSD
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$maitriseController = $app['controllers_factory'];
$maitriseController->before($mustBeLogged);

$maitriseController->get('/list', function() use ($app) {
	$session = $app['session']->get('user');
	$accessibleUnite = $app['userService']->getAccessibleUnite($session['id']);
	return getCorrectResponse($accessibleUnite);
});

$maitriseController->get('/all', function() use ($app) {
	$unites = $app['userService']->getAllUnite();
	return getCorrectResponse($unites);
});

$maitriseController->get('/detail', function(Request $request) use ($app) {
	$id = $request->get('id');
	$app['monolog']->addDebug("Id Unite : " . $id);
	$unite = $app['uniteService']->getInformation($id);
	$app['monolog']->addDebug("Id Unite Result : " . $unite['id']);
	return getCorrectResponse($unite);
});

$maitriseController->get('/chefs', function(Request $request) use ($app) {
	$app['monolog']->addDebug("Chefs : ");
	$all = $request->get('all');
	if($all == 1) {
		$app['monolog']->addDebug("Chefs All ");
		$chefs = $app['userService']->getList();
	} else {
		$app['monolog']->addDebug("Chefs Dispo ");
		$chefs = $app['userService']->getDispoList();
	}
	return getCorrectResponse($chefs);
});

$maitriseController->get('/maitrise', function(Request $request) use ($app) {
	$id = $request->get('id');
	$maitrise = $app['uniteService']->getMaitrise($id);
	return getCorrectResponse($maitrise);
});

$maitriseController->post('/update_unite', function(Request $request) use ($app) {
	$unite = json_decode($request->getContent());
	$app['uniteService']->updateUnite($unite);
	return new Response("Mise à jour effectué", 200);
});

$maitriseController->post('/maitrise_add', function(Request $request) use ($app) {
	$payload = json_decode($request->getContent());
	$idUnite = $payload->id;
	$idUser = $payload->chef;
	$app['uniteService']->addUserInMaitrise($idUnite, $idUser);
	return new Response("Mise à jour effectué", 200);
});

$maitriseController->post('/maitrise_delete', function(Request $request) use ($app) {
	$payload = json_decode($request->getContent());
	$idUnite = $payload->id;
	$idUser = $payload->chef;
	$app['uniteService']->deleteUserInMaitrise($idUnite, $idUser);
	return new Response("Mise à jour effectué", 200);
});

$app->mount('/unite', $maitriseController);

?>
