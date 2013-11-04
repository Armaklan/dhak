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


$authentController->post('/login', function(Request $request) use($app) {

    try {
        $payload = json_decode($request->getContent());
        $login = $payload->login;
        $password = $payload->pass;
        $app["userService"]->login($login, $password);
    } catch (Exception $e) {
        $errorMsg = "Login ou mots de passe incorrect";
        return new Response('Login or password incorrect', 400);
    }

	return new Response('Login successfull', 200);

});

$authentController->get('/active', function(Request $request) use($app) {

    try {
        if($app['session']->get('user') != null) {
            return getCorrectResponse($app['session']->get('user'));
        } else {
            return new Response('Non authentifie', 400);
        }
    } catch (Exception $e) {
        return new Response('Non authentifie' . $e->getMessage() , 400);
    }

});

$authentController->get('/logout', function() use($app) {
    $app["userService"]->logout();
    return new Response("Logout réussit", 200);
});

$authentController->get('/list', function() use($app) {
    $users = $app["userService"]->all();
    return getCorrectResponse($users);
});

$authentController->get('/formations', function() use($app) {
    $formations = $app["userService"]->getFormations();
    return getCorrectResponse($formations);
});

$authentController->post('/create_user', function(Request $request) use ($app) {
    $user = json_decode($request->getContent());
    $id = $app['userService']->createUser($user);
    $user->id = $id;
    return getCorrectResponse($user);
});


$authentController->post('/update_user', function(Request $request) use ($app) {
    $user = json_decode($request->getContent());
    $id = $app['userService']->updateUser($user);
    $user->id = $id;
    return getCorrectResponse($user);
});

$authentController->post('/update_pwd', function(Request $request) use ($app) {
    $payload = json_decode($request->getContent());
    $id = $payload->id;
    $password = $payload->password;
    $app['userService']->changePassword($id, $password);
    return new Response("Mise à jour réussit", 200);
});


$authentController->post('/update_right', function(Request $request) use ($app) {
    $payload = json_decode($request->getContent());
    $app['userService']->addRight($payload->user, $payload->unites);
    return new Response("Modification effectués", 200);
});

$authentController->get('/detail', function(Request $request) use ($app) {
    $id = $request->get('id');
    $user = $app['userService']->getById($id);
    return getCorrectResponse($user);
});

$authentController->get('/profil', function(Request $request) use ($app) {
    $id = $app['session']->get('user')['id'];
    $user = $app['userService']->getById($id);
    return getCorrectResponse($user);
});

$authentController->get('/profils', function(Request $request) use ($app) {
    $profils = $app['userService']->getProfils();
    return getCorrectResponse($profils);
});

$app->mount('/', $authentController);

?>
