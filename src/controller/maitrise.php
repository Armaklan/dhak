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
	$requirement = new UniteRequirement($app["monolog"],$unite['size'], $maitrise);
	return $app->render('maitrise.html.twig', [ 'accessible_unite' => $accessibleUnite, 'unite' => $unite, 
		'maitrise' => $maitrise, 'list_chef' => $listChef, 'formations' => $formations, 'requirement' => $requirement]);
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
	$nbSizaine = $request->get('nb_sizaine');
	$commentaire = $request->get('commentaire');
	$app['uniteService']->updateSize($idUnite, $size, $nbSizaine, $commentaire);
    return $app->redirect($app->path('maitrise_synthese', array()));
})->bind('unite_modif_size');

$maitriseController->get('/create_user', function(Request $request) use($app) {
	$formations = $app['userService']->getFormations();
	$profils = $app['userService']->getProfils();
	$unite = $app['userService']->getAllUnite();
	return $app->render('create_user.html.twig', ['formations' => $formations, 'unites' => $unite, 'profils' => $profils]);
})->bind('create_user');

$maitriseController->post('/save_user', function(Request $request) use($app) {
	$user = $app['userService']->getUserAllFromRequest($request);
	$idChef = $app['userService']->createUser($user);
	$unites = $request->get("inputUnites");
	$app['monolog']->addDebug("Unite : " . $unites);
	$app['userService']->addRight($idChef, $unites);
    $app["session"]->getFlashBag()->add('success', "Utilisateur crée avec succès");
    return $app->redirect($app->path('create_user', array()));
})->bind('save_user');
$app->mount('/maitrise', $maitriseController);

class UniteRequirement{


	private $log;
	public $actual;
	public $currentReq;
	public $shortActReq;
	public $longActReq;
	public $currentReqChecked;
	public $shortActReqChecked;
	public $longActReqChecked;

	public function __construct($logger, $size, $maitrise)
    {
		$this->log = $logger;
		$this->actual = array();
		$this->currentReq = array();
		$this->shortActReq = array();
		$this->longActReq = array();
		$this->calculCurrentReq($size);
		$this->calculShortActReq($size);
		$this->calculLongActReq($size);
		$this->initializeActual($maitrise);
		$this->currentReqChecked = $this->checkReq($this->currentReq);
		$this->shortActReqChecked = $this->checkReq($this->shortActReq);
		$this->longActReqChecked = $this->checkReq($this->longActReq);
	}

	public function calculCurrentReq($size) {
		$this->currentReq[3] = 0;
		$this->currentReq[2] = 0;
		$this->currentReq[1] = 1;
		$this->currentReq[0] = 2;
		if($size > 12) {
			$this->currentReq[1]++;
			$this->currentReq[0]++;
		}
	}

	public function calculShortActReq($size) {
		$this->shortActReq[3] = 0;
		$this->shortActReq[2] = 0;
		$this->shortActReq[1] = 1;
		$this->shortActReq[0] = ((int) (($size - 1) / 6)) + 1;
		if($size > 12) {
			$this->shortActReq[1] = 0;
			$this->shortActReq[2] = 2;
		}
	}

	public function calculLongActReq($size) {
		$this->longActReq[3] = 1;
		$this->longActReq[2] = 1;
		$this->longActReq[1] = 0;
		$this->longActReq[0] = ((int) (($size - 1) / 6)) + 2;
		if($size > 12) {
			$this->longActReq[1] = 1;
		}
	}

	public function initializeActual($maitrise) {
		$this->actual[3] = 0;
		$this->actual[2] = 0;
		$this->actual[1] = 0;
		$this->actual[0] = 0;
		foreach($maitrise as $chef) {
			if($chef['formation_lvl'] > 0 ) {
				$this->actual[$chef['formation_lvl']]++;
			}
			$this->actual[0]++;
		}
	}

	public function checkReq($currentReq) {
		$tmpActual = $this->actual;

		$currentReq[3] = $currentReq[3] - $tmpActual[3];
		$this->log->addDebug("CEP3 : " . $currentReq[3]);
		$calculRetains = 0;
		if ($currentReq[3] <= 0) {
			$calculRetains = abs($currentReq[3]);
			$this->log->addDebug("Retains : " . $calculRetains);
		} else {
			return false;
		}

		$currentReq[2] = $currentReq[2] - $tmpActual[2] - $calculRetains;
		$calculRetains = 0;
		if ($currentReq[2] <= 0) {
			$calculRetains = abs($currentReq[2]);
		} else {
			return false;
		}

		$currentReq[1] = $currentReq[1] - $tmpActual[1] - $calculRetains;
		$currentReq[0] = $currentReq[0] - $tmpActual[0];

		if(($currentReq[1] > 0) || $currentReq[0] > 0)  {
			return false;
		}

		return true;
	}

	public function getTempActual() {
		$tmpActual = array();
		$tmpActual[0] = $this->actual[0];
		$tmpActual[1] = $this->actual[1];
		$tmpActual[2] = $this->actual[2];
		$tmpActual[3] = $this->actual[3];
		return $tmpActual;
	}
}

?>
