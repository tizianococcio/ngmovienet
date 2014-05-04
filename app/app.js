

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
				.when('/liste/nuova',
				{
					controller: 'newListController',
					templateUrl: baseFolder + '/app/partials/lista_crea.html',
					controllerAs: 'ctrl'
				})
				.when('/liste/associa/:movieId/:listaId',
				{
					controller: 'ListController',
					controllerAs: 'ctrl'
				})				
				.otherwise({ redirectTo: '/' });
		}]);