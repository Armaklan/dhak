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

$campController = $app['controllers_factory'];
$campController->before($mustBeLogged);

$campController->get('/detail', function(Request $request) use ($app) {
	$id = $request->get('id');
	$unite = $app['campService']->getInformation($id);
	return getCorrectResponse($unite);
});

$campController->post('/update', function(Request $request) use ($app) {
	$payload = json_decode($request->getContent());
	$id = $payload->id;
	$detail = $payload->detail;
	$app['campService']->updateInformation($id, $detail);
	return getCorrectResponse("Ok");
});


$campController->post('/chef_update', function(Request $request) use ($app) {
	$payload = json_decode($request->getContent());
	$id = $payload->id;
	$chefs = $payload->chefs;
	$app['campService']->updateChefPresence($id, $chefs);
	return getCorrectResponse("Ok");
});

$campController->get('/maitrise', function(Request $request) use ($app) {
	$id = $request->get('id');
	$maitrise = $app['campService']->getMaitrise($id);
	return getCorrectResponse($maitrise);
});

$campController->post('/maitrise_add', function(Request $request) use ($app) {
	$payload = json_decode($request->getContent());
	$idUnite = $payload->id;
	$idUser = $payload->chef;
	$app['campService']->addUserInMaitrise($idUnite, $idUser);
	return new Response("Mise à jour effectué", 200);
});

$campController->post('/maitrise_delete', function(Request $request) use ($app) {
	$payload = json_decode($request->getContent());
	$idUnite = $payload->id;
	$idUser = $payload->chef;
	$app['campService']->deleteUserInMaitrise($idUnite, $idUser);
	return new Response("Mise à jour effectué", 200);
});

$app->mount('/camp', $campController);

?>
