function UserCtrl($scope, AuthentService, $location, authenticatedUser) {

    $scope.user = {
        "login": authenticatedUser.username,
        "pass": ""
    };

    $scope.msgs = [];

    $scope.logon = function(loginForm) {
    	if(loginForm.$valid) {
    		AuthentService.login(
    			$scope.user,
    			function($data) {
    				authenticatedUser.username = $scope.user.login;
    				authenticatedUser.isLogged = true;
					$location.path(authenticatedUser.targetUrl);
    			},
    			function() {
    				$scope.msgs[0] = "Login ou mot de passe incorrect";
    			}
    		);
    	}
    };
}

function UserListCtrl($scope, $location, UserService) {
	$scope.users = [];
	$scope.sort = "username";
	$scope.reverse = false;

	UserService.list([],
		function(data) {
			$scope.users = data;
		}
	);

	$scope.detail = function(id) {
		$location.path('user/' + id);
	}
}

function UniteListCtrl($scope, $location, UniteService) {

	$scope.list_unite = [];
	$scope.sort = "groupe_name";
	$scope.reverse = false;

	UniteService.listAccessible([],
		function(data) {
			$scope.list_unite = data;
		}
	);

	$scope.detail = function(id) {
		$location.path('unite/' + id);
	};

}

function UniteCtrl($scope, $location, $routeParams, $modal, AuthentService, UniteService, UniteRequirementService) {
	$scope.breadcrumb = "Modifier";
	$scope.unite = {};
	$scope.maitrise = [];
	$scope.list_chef = [];
	$scope.errors = [];
	$scope.success = [];
	$scope.currentRequirement = [];
	$scope.shortRequirement = [];
	$scope.longRequirement = [];
	$scope.currentMaitrise = [];
	$scope.checked = {};
	$scope.selected_chef = 0;

	UniteService.get(
		{id: $routeParams.id},
		function(data) {
			$scope.unite = data;
			$scope.breadcrumb = "Modifier " + data.branche_name + " - " + data.groupe_name;
			$scope.currentRequirement = UniteRequirementService.calculCurrentRequirement(data);
			$scope.shortRequirement = UniteRequirementService.calculShortActReqRequirement(data);
			$scope.longRequirement = UniteRequirementService.calculLongActReqRequirement(data);

			UniteService.maitrise(
				{id: $routeParams.id},
				function(data) {
					$scope.maitrise = data;
					$scope.currentMaitrise = UniteRequirementService.calculCurrentMaitrise(data);

					$scope.checked = {
						current: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.currentRequirement),
						shortActivity: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.shortRequirement),
						longActivity: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.longRequirement)
					};

				}, 
				function() {
					$scope.errors.push("Impossible de récupérer la maitrise actuelle");
				}
			);
		}, 
		function() {
			alert("erreur dans la récuperation des données");
		}
	);

	UniteService.chefs(
		{},
		function(data) {
			$scope.list_chef = data;
		}, 
		function() {
			$scope.errors.push("Impossible de récupérer les chefs disponibles");
		}
	);



	$scope.technicalAddChef=function(selected) {
		$scope.maitrise.push(selected);

		var index = -1;
		var id = 0;
		$scope.list_chef.forEach(function(chef) {
			if(selected.id == chef.id) {
				index = id;
			} 
			id++;
		});
		if (index > -1) {
		    $scope.list_chef.splice(index, 1);
		}
		$scope.selected_chef = {};

		$scope.currentMaitrise = UniteRequirementService.calculCurrentMaitrise($scope.maitrise);
		UniteService.maitriseAdd(
			{id: $routeParams.id,
			 chef: selected.id},
			function() {
			}, 
			function() {
				$scope.errors.push("Impossible de modifier la maitrise");
			}
		);

		$scope.checked = {
			current: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.currentRequirement),
			shortActivity: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.shortRequirement),
			longActivity: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.longRequirement)
		};
	}

	$scope.addChef=function() {
		selected = JSON.parse($scope.selected_chef);
		$scope.technicalAddChef(selected);
	}

	$scope.detach=function(chef) {
		var index = $scope.maitrise.indexOf(chef);
		if (index > -1) {
		    $scope.maitrise.splice(index, 1);
		}
		$scope.currentMaitrise = UniteRequirementService.calculCurrentMaitrise($scope.maitrise);
		$scope.list_chef.push(chef);
		UniteService.maitriseDelete(
			{id: $routeParams.id,
			 chef: chef.id},
			function() {
			}, 
			function() {
				$scope.errors.push("Impossible de modifier la maitrise");
			}
		);

		$scope.checked = {
			current: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.currentRequirement),
			shortActivity: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.shortRequirement),
			longActivity: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.longRequirement)
		};
	}

	$scope.saveUnitModif=function() {
		UniteService.update(
			$scope.unite,
			function() {
				$scope.success[0] = "Mise à jour effectué";
			}, 
			function() {
				$scope.errors[0] = "Mise à jour impossible";
			}
		);

		$scope.currentRequirement = UniteRequirementService.calculCurrentRequirement($scope.unite);
		$scope.shortRequirement = UniteRequirementService.calculShortActReqRequirement($scope.unite);
		$scope.longRequirement = UniteRequirementService.calculLongActReqRequirement($scope.unite);

		$scope.checked = {
			current: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.currentRequirement),
			shortActivity: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.shortRequirement),
			longActivity: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.longRequirement)
		};
	}

	$scope.goBack=function() {
		$location.path('/unite/list');
	}

	$scope.items = ['item1', 'item2', 'item3'];

	$scope.editUser = function(chef) {

	    var modalInstance = $modal.open({
	    	templateUrl: 'views/user/modal.html',
	      	controller: ModalUserCtrl,
	      	resolve: {
	        	chef: function () {
	        		return jQuery.extend({}, chef);
	        	}
	      	}
	    });

	    modalInstance.result.then(function (updatedChef) {
		    chef.long_name = updatedChef.long_name;
		    chef.firstname = updatedChef.firstname;
		    chef.post_code = updatedChef.post_code;
		    chef.adresse = updatedChef.adresse;
		    chef.formation_lvl = updatedChef.formation_lvl;
		    chef.formation_name = updatedChef.formation_name;
		    chef.city = updatedChef.city;
		    chef.tel = updatedChef.tel;
		    chef.birthday = updatedChef.birthday;
		    chef.mail = updatedChef.mail;
		    chef.commentaire = updatedChef.commentaire;
		    chef.profil = updatedChef.profil;

       		AuthentService.update(chef);
	    });
	};

	$scope.createUser = function() {

		chef = {
		  long_name : "",
	      firstname : "",
	      post_code : "",
	      adresse : "",
	      formation_lvl : "",
	      city : "",
	      tel : "",
	      birthday : "",
	      mail : "",
	      commentaire : "",
	      profil : "",
	      password: "",
	      username: ""
		};
	    var modalInstance = $modal.open({
	    	templateUrl: 'views/user/modal.html',
	      	controller: ModalUserCtrl,
	      	resolve: {
	        	chef: function () {
	          		return chef;
	        	}
	      	}
	    });

	    modalInstance.result.then(function (updatedChef) {
	    	updatedChef.username = updatedChef.long_name + updatedChef.firstname;

	    	AuthentService.create(updatedChef, function(data){
	    		$scope.technicalAddChef(data);
	    	});

	    });
	};


}

var ModalUserCtrl = function ($scope, $modalInstance, AuthentService, chef) {
  	$scope.chef = chef;

	AuthentService.formations(
		{},
		function(data) {
			$scope.formations = data;
		}
	);

  $scope.ok = function () {
  	$scope.formations.forEach(function (elt) {
  		if(elt.lvl == $scope.chef.formation_lvl) {
  			$scope.chef.formation_name = elt.name;
  		}
  	});
    $modalInstance.close($scope.chef);
  };

  $scope.cancel = function () {
    $modalInstance.dismiss('cancel');
  };
};

function UserDetailCtrl($scope, $location, $routeParams, AuthentService, UniteService) {

	$scope.right=[];
	$scope.msg_right = [];
	$scope.msg_mdp = [];
	$scope.err_mdp = [];
	$scope.breadcrumb = "";
	$scope.target = "/user/list";
	$scope.admin = true;

	AuthentService.formations(
		{},
		function(data) {
			$scope.formations = data;
		}
	);

	AuthentService.profils(
		{},
		function(data) {
			$scope.profils = data;
		}
	);

	UniteService.all(
		{},
		function(data) {
			$scope.unites = data;
		}
	);

	if($routeParams.id == "my") {
		$scope.admin = false;
		$scope.target = "/";
		AuthentService.my({},function(data) {
			$scope.chef = data;
			$scope.breadcrumb = "Modifier " + data.username;
		});
	} else if($routeParams.id != "0") {
		AuthentService.get({id: $routeParams.id},function(data) {
			$scope.chef = data;
			$scope.breadcrumb = "Modifier " + data.username;
		});
	} else {
		$scope.breadcrumb = "Création";
		$scope.chef = {
		  id: 0,
		  long_name : "",
	      firstname : "",
	      post_code : "",
	      adresse : "",
	      formation_lvl : "",
	      city : "",
	      tel : "",
	      birthday : "",
	      mail : "",
	      commentaire : "",
	      profil : "",
	      password: "",
	      username: ""
		};
	}


	$scope.create_or_update = function() {
		if($scope.chef.id != 0) {
			AuthentService.update($scope.chef);
		} else {
			AuthentService.create($scope.chef);
		}
	}

	$scope.ok = function() {
		$scope.create_or_update();
		$location.path($scope.target);
	}

	$scope.cancel = function() {
		$location.path($scope.target);
	}

	$scope.ok_right = function() {
		$scope.create_or_update();
		AuthentService.update_right({
			user:$scope.chef.id,
			unites:$scope.right
		}, function() {
			$scope.err_right.push("Modification effectué");
		});
		
	}

	$scope.change_mdp = function() {
		if ($scope.password1 != $scope.password2) {
			$scope.err_mdp.push("Les mots de passe saisis sont différents.");
		} else if($scope.password1.length < 5) {
			$scope.err_mdp.push("Les mots de passe doit avoir à minima 5 caractères.");
		} else {
			AuthentService.update_pwd({
				id:$scope.chef.id,
				password:$scope.password1
			}, function() {
				$scope.msg_mdp.push("Mot de passe mis à jour.");
			})
		}
	}
}

function MenuCtrl($scope, $location, authenticatedUser, AuthentService) {
	$scope.user = authenticatedUser;

	$scope.logout = function() {
		AuthentService.logout();
		authenticatedUser.username = "";
		authenticatedUser.isLogged = false;
		authenticatedUser.targetUrl = "/";
		$location.path('/login');
	};
}

function MessageCtrl($scope, $location) {

	$scope.tinymceOptions = {
        'height': '300px'
    };

}

function ProjetCampListCtrl($scope, $location, $routeParams, UniteService) {
	$scope.list_unite = [];
	$scope.sort = "groupe_name";
	$scope.reverse = false;

	UniteService.listAccessible([],
		function(data) {
			$scope.list_unite = data;
		}
	);

	$scope.detail = function(id) {
		$location.path('camp/' + id);
	};

}

function ProjetCampCtrl($scope, $location, $routeParams, UniteRequirementService, UniteService) {


	UniteService.get(
		{id: $routeParams.id},
		function(data) {
			$scope.unite = data;
			$scope.breadcrumb = "Projet de camps " + data.branche_name + " - " + data.groupe_name;
			$scope.currentRequirement = UniteRequirementService.calculCurrentRequirement(data);
			$scope.shortRequirement = UniteRequirementService.calculShortActReqRequirement(data);
			$scope.longRequirement = UniteRequirementService.calculLongActReqRequirement(data);

			UniteService.maitrise(
				{id: $routeParams.id},
				function(data) {
					$scope.maitrise = data;
					$scope.currentMaitrise = UniteRequirementService.calculCurrentMaitrise(data);

					$scope.checked = {
						current: UniteRequirementService.checkRequirement($scope.currentMaitrise, $scope.currentRequirement)
					};

				}, 
				function() {
					$scope.errors.push("Impossible de récupérer la maitrise actuelle");
				}
			);
		}, 
		function() {
			alert("erreur dans la récuperation des données");
		}
	);

}