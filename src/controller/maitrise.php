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

$maitriseController->get('/', function() use ($app) {
	$idUnite = $app['session']->get('selectedUnite');
	$maitrise = null;
	$accessibleUnite = $app['userService']->getAccessibleUnite($app['session']->get('user')['id']);
	$unite = $app['uniteService']->getInformation($idUnite);
	$maitrise = $app['uniteService']->getMaitrise($idUnite);
    $listChef = $app['userService']->getList();
	$formations = $app['userService']->getFormations();
	return $app->render('maitrise.html.twig', [ 'accessible_unite' => $accessibleUnite, 'unite' => $unite, 
		'maitrise' => $maitrise, 'list_chef' => $listChef, 'formations' => $formations]);
})->bind("maitrise_synthese");

$maitriseController->post('/change_unite', function(Request $request) use ($app) {
	$selectedUnite = $request->get('selectedUnite');
	$app['session']->set('selectedUnite', $selectedUnite);
    return $app->redirect($app->path('maitrise_synthese', array()));
})->bind("change_unite");

$maitriseController->post('/add', function(Request $request) use ($app) {
	$idUnite = $app['session']->get('selectedUnite');
    $selectedChef = $request->get('selectedChef');
    try {
        $app['uniteService']->addUserInMaitrise($idUnite, $selectedChef);
    } catch (Exception $e) {
        $app['monolog']->error($e->getMessage());
    }
    return $app->redirect($app->path('maitrise_synthese', array()));
})->bind("maitrise_add");

$maitriseController->get('/del/{idUser}', function($idUser) use ($app) {
	$idUnite = $app['session']->get('selectedUnite');
    try {
        $app['uniteService']->deleteUserInMaitrise($idUnite, $idUser);
    } catch (Exception $e) {
        $app['monolog']->error($e->getMessage());
    }
    return $app->redirect($app->path('maitrise_synthese', array()));
})->bind("maitrise_del");

$maitriseController->post('/create', function(Request $request) use($app) {
	$idUnite = $app['session']->get('selectedUnite');
	$user = $app['userService']->getUserFromRequest($request);
	$idChef = $app['userService']->createUser($user);
    $app['uniteService']->addUserInMaitrise($idUnite, $idChef);
    return $app->redirect($app->path('maitrise_synthese', array()));
})->bind('maitrise_create');

$maitriseController->post('/update', function(Request $request) use($app) {
	$idUnite = $app['session']->get('selectedUnite');
	$user = $app['userService']->getUserFromRequest($request);
	$app['userService']->updateUser($user);
    return $app->redirect($app->path('maitrise_synthese', array()));
})->bind('maitrise_update');

$maitriseController->post('/size', function(Request $request) use($app) {
	$idUnite = $request->get('id');
	$size = $request->get('size');
	$app['uniteService']->updateSize($idUnite, $size);
    return $app->redirect($app->path('maitrise_synthese', array()));
})->bind('unite_modif_size');

$app->mount('/maitrise', $maitriseController);

?>
