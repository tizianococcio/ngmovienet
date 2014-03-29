		angular.module('configuration', [])
		       .constant('CONFIGURATION', {
		       		root: '/ngMovieNet/'
		       	});

		var app = angular.module('movieNetApp', ['ngRoute', 'ui.bootstrap', 'configuration']);
		
		app.config(function($routeProvider, $locationProvider){
			var baseFolder = '/ngMovieNet';
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
					controller: 'detailsMovieController',
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
				.otherwise({ redirectTo: '/' });
		});