var app = angular.module('movieNetApp', 
	['ngRoute', 'ui.bootstrap', 'configuration', 'angularFileUpload', 'ng-remote-validator-directive', 'simplePagination']);

app.config(['$routeProvider', '$locationProvider', 'CONFIGURATION', function($routeProvider, $locationProvider, conf){

	var baseFolder = conf.root;
	$routeProvider
		.when('/test',
		{
			controller: 'testController',
			templateUrl: baseFolder + '/app/partials/test.html',
			controllerAs: 'ctrl'
		})			
		.when('/',
		{
			controller: 'homeController',
			templateUrl: baseFolder + '/app/partials/home.html',
			controllerAs: 'ctrl'
		})
		.when('/movies',
		{
			controller: 'movieController',
			templateUrl: baseFolder + '/app/partials/lista.html',
			controllerAs: 'ctrl'
		})
		.when('/movies/byDirector/:director_id',
		{
			controller: 'movieController',
			templateUrl: baseFolder + '/app/partials/lista.html',
			controllerAs: 'ctrl'
		})				
		.when('/movie/:id',
		{
			controller: 'showMovieController',
			templateUrl: baseFolder + '/app/partials/moviedetail.html',
			controllerAs: 'ctrl'
		})
		.when('/nuovo-film',
		{
			controller: 'newMovieController',
			templateUrl: baseFolder + '/app/partials/nuovofilm.html',
			controllerAs: 'ctrl'
		})
		.when('/movie/edit/:id',
		{
			controller: 'detailsMovieController',
			templateUrl: baseFolder + '/app/partials/modificafilm.html',
			controllerAs: 'ctrl'
		})	
		.when('/liste',
		{
			controller: 'ListController',
			templateUrl: baseFolder + '/app/partials/lista_liste.html',
			controllerAs: 'ctrl'
		})
		.when('/liste/:id',
		{
			controller: 'detailsListController',
			templateUrl: baseFolder + '/app/partials/lista_dettaglio.html',
			controllerAs: 'ctrl'
		})
		.when('/liste/nuova',
		{
			controller: 'newListController',
			templateUrl: baseFolder + '/app/partials/lista_crea.html',
			controllerAs: 'ctrl'
		})
		.when('/directors',
		{
			controller: 'directorsListCtrl',
			templateUrl: baseFolder + '/app/partials/directors_list.html',
			controllerAs: 'ctrl'
		})
		.otherwise({ redirectTo: '/' });
}]);

app.run(['CONFIGURATION', '$route', '$rootScope', function(conf, $route, $rootScope)
{
	$rootScope.path = function(controller, params)
	{
		// Iterate over all available routes
		for(var path in $route.routes)
		{
			var pathController = $route.routes[path].controller;

			if(pathController == controller) // Route found
			{
				var result = path;

				// Construct the path with given parameters in it
				for(var param in params)
				{
					result = conf.root + '#' + result.replace(':' + param, params[param]);
				}

				return result;
			}
		}

		// No such controller in route definitions
		return undefined;
	};	
}]);

