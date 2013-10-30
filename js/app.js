angular.module("DhakModule", ['ngResource', 'ui.select2', 'ui.bootstrap', 'DhakService', 'DhakDirectives'])
	.config(['$routeProvider', function($routeProvider) {
	    $routeProvider.
	    	when('/login', {templateUrl: 'views/login.html',   controller: UserCtrl, isSecured: false}).
	        when('/test', {templateUrl: 'views/test.html',   controller: UserCtrl, isSecured: true}).
	        when('/unite/list', {templateUrl: 'views/unite/list.html',   controller: UniteListCtrl, isSecured: true}).
	    	when('/unite/:id', {templateUrl: 'views/unite/detail.html',   controller: UniteCtrl, isSecured: true}).
	    	when('/user/list', {templateUrl: 'views/user/list.html',   controller: UserListCtrl, isSecured: true}).
	    	when('/user/:id', {templateUrl: 'views/user/detail_prv.html',   controller: UserDetailCtrl, isSecured: true}).
	        otherwise({redirectTo: '/test'});
	}])
	.run(['$rootScope', 'authenticatedUser', '$location', '$route', 'ActiveUserService', function(root, authenticatedUser, $location, $route, ActiveUserService) {
		root.$on('$routeChangeStart', function(event, next, current) { 

			if (next.isSecured && !authenticatedUser.isLogged) {

				ActiveUserService.query(
					[],
					function(data) {
						authenticatedUser.isLogged = true;
						authenticatedUser.username = data.username;
						$route.reload();
					},
					function() {
						var url = $location.path() + "";
						if(url != "/login") {
							authenticatedUser.targetUrl = url;
						}
						$location.path("/login");
					}
				);	
			}

		});
	}]);
