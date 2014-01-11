var dhakService = angular.module("DhakService", ['ngResource']);

dhakService.factory('authenticatedUser', ['$resource', 
	function($resource) {
		var user = {
			isLogged : false,
			targetUrl : '/',
			username : ''
		};
	return user;
}]);

dhakService.factory('AuthentService', ['$resource',
  function($resource){

    return $resource('api/:action', {}, {
      login: {method:'POST', params: {action: 'login'}},
      logout: {method:'GET', params: {action: 'logout'}},
      formations: {method:'GET', isArray: true, params: {action: 'formations'}},
      create: {method:'POST', isArray: false, params: {action: 'create_user'}},
      update: {method:'POST', isArray: false, params: {action: 'update_user'}},
      get: {method:'GET', isArray: false, params: {action: 'detail'}},
      my: {method:'GET', isArray: false, params: {action: 'profil'}},
      profils: {method:'GET', isArray: true, params: {action: 'profils'}},
      update_right: {method:'POST', isArray: false, params: {action: 'update_right'}},
      update_pwd: {method:'POST', isArray: false, params: {action: 'update_pwd'}},
    });
  }
]);

dhakService.factory('ActiveUserService', ['$resource',
  function($resource){

    return $resource('api/active', {}, {
      query: {method:'GET'}
    });
  }
]);

dhakService.factory('UserService', ['$resource',
  function($resource){

    return $resource('api/:action', {}, {
      list: {method:'GET', isArray: true, params: {action : 'list'}},
    });
  }
]);

dhakService.factory('UniteService', ['$resource',
  function($resource){

    return $resource('api/unite/:action', {}, {
      listAccessible: {method:'GET', isArray: true, params: {action : 'list'}},
      all: {method:'GET', isArray: true, params: {action : 'all'}},
      get: {method:'GET', isArray: false, params: {action: 'detail'}},
      chefs: {method:'GET', isArray: true, params: {action: 'chefs'}},
      update: {method:'POST', isArray: false, params: {action: 'update_unite'}},
      maitrise: {method:'GET', isArray: true, params: {action: 'maitrise'}},
      maitriseAdd: {method:'POST', isArray: false, params: {action: 'maitrise_add'}},
      maitriseDelete: {method:'POST', isArray: false, params: {action: 'maitrise_delete'}},
    });
  }
]);

dhakService.factory('CampService', ['$resource',
  function($resource){

    return $resource('api/camp/:action', {}, {
      get: {method:'GET', isArray: false, params: {action: 'detail'}},
      update: {method:'POST', isArray: false, params: {action: 'update'}},
      update_chef: {method:'POST', isArray: false, params: {action: 'chef_update'}},
    });
  }
]);

dhakService.factory('UniteRequirementService', ['$resource',
  function(){

    return {

      calculCurrentRequirement : function(unite) {
        currentReq = {total: 2,ac: 1,cep1: 0,cep2: 0};

        if(unite.size > 12) {
          currentReq.ac++;
          currentReq.total++;
        }

        return currentReq;
      }, 

      calculShortActReqRequirement : function(unite) {
        shortReq = {total: 0,ac: 1,cep1: 1,cep2: 0};

        shortReq.total = parseInt((unite.size -1) / 6) + 1;
        if(unite.size > 12) {
          shortReq.ac--;
          shortReq.cep1++;
        }

        return shortReq;
      }, 

      calculLongActReqRequirement : function(unite) {
        longReq = {total: 0, ac: 0, cep1: 1, cep2: 1};

        longReq.total = parseInt((unite.size -1) / 6) + 2;
        if(unite.size > 12) {
          longReq.ac++;
        }

        return longReq;
      },

      calculCurrentMaitrise : function(maitrise) {
        currentMaitrise = {total: 0, ac: 0, cep1: 0, cep2: 0};
        maitrise.forEach(function(chef) {
          currentMaitrise.total++;
          switch(chef.formation_lvl){ 
            case '1': 
              currentMaitrise.ac++;
              break; 
            case '2': 
              currentMaitrise.cep1++;
              break; 
            case '3': 
              currentMaitrise.cep2++; 
              break;
            default: 
              break; 
          } 
        });
        return currentMaitrise;
      },

      checkRequirement : function(currentMaitrise, requirement) {
        var tmpMaitrise = jQuery.extend({}, currentMaitrise);

        tmpMaitrise.cep2 = tmpMaitrise.cep2 - requirement.cep2;
        if(tmpMaitrise.cep2 < 0) {
          return "danger";
        } 

        tmpMaitrise.cep1 = tmpMaitrise.cep1 + tmpMaitrise.cep2 - requirement.cep1;
        if(tmpMaitrise.cep1 < 0) {
          return "danger";
        }

        tmpMaitrise.ac = tmpMaitrise.ac + tmpMaitrise.cep1 - requirement.ac;
        if(tmpMaitrise.ac < 0) {
          return "danger";
        }

        if(tmpMaitrise.total < requirement.total) {
          return "danger";
        }

        return "success";
      },


    };
  }
]);

