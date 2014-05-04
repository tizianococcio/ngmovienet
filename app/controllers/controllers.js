 Date.prototype.yyyymmdd = function() {
	var yyyy = this.getFullYear().toString();
	var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
	var dd  = this.getDate().toString();
	return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
  };
 
var controllers = {};

// Used for testing
controllers.testController = function ($fileUploader, $scope){
	var _this = this;
	
	init();
	
	function init() {	
        // create a uploader with options
        var uploader = $scope.uploader = $fileUploader.create({
            scope: $scope,                          // to automatically update the html. Default: $rootScope
            url: '/ngMovieNet/ws/upload/locandina'
            //url: '/ngMovieNet/upload.php'
        });

        // REGISTER UPLOADER HANDLERS
        uploader.bind('afteraddingfile', function (event, item) {
            console.info('After adding a file', item);
        });    
	}

};

// New List Controller
controllers.newListController = function (listaFactory){
	

	var _this = this;
	
	init();
	
	function init() {	

		// Form submit status
		_this.submitStatus = false;

		// messagio salvataggio
		_this.messaggio = "";
	}

	_this.salva = function() {
		listaFactory.setLista(_this.lista).success(function(data){
			if (data.status === 'ok')
			{
				_this.submitStatus = true;
				_this.lista = [];
				_this.messaggio = "Lista creata";
			}
			else
			{
				_this.messaggio = "Errore.";
			}
		});
	};

};

controllers.ListController = function ($routeParams){
	

	var _this = this;
	
	init();
	
	function init() {	
		console.log($routeParams.movieId);
		console.log($routeParams.listaId);
	}

};

// Home page controller
controllers.homeController = function (){
	

	var _this = this;

	_this.names = [];
	
	init();
	
	function init() {	
	}

};

// Lista film
controllers.movieController = function ($scope, movieFactory, listaFactory, directorsFactory, Pagination, $routeParams) {

	var _this = this;

	_this.movies = [];

	_this.liste = [];

	_this.director = [];

	init();
	
	function handleSuccess(data, status) {

		_this.pagination = Pagination.getNew();
		_this.pagination.perPage = 15;
		_this.pagination.range = 2;

		_this.movies = data;

		_this.pagination.numPages = Math.ceil(_this.movies.length/_this.pagination.perPage);
	}

	function handleListeSuccess(data, status) {
		_this.liste = data;
	}	

	function handleDirectorSuccess(data, status) {
		_this.director = data;
	}
	
	function init() {

		// Param director_id in GET request - filtering movies by director
		var directorId = $routeParams.director_id ? $routeParams.director_id : 0;

		if (directorId > 0)
		{
			// Must filter movies by director
			movieFactory.getMoviesByDirector(directorId).success(handleSuccess);

			// Get director info
			directorsFactory.getDirector(directorId).success(handleDirectorSuccess);
		}
		else
		{
			movieFactory.getMovies().success(handleSuccess);	
		}

		_this.liste = listaFactory.getListe();

		$scope.play = function() {
			console.log('OK')
		}
	}

	listaFactory.getListe().success(handleListeSuccess);
}

// mostra
controllers.showMovieController = function ($routeParams, $fileUploader, $scope, movieFactory, CONFIGURATION) {
	var _this = this;

	_this.movie = {};

	init();

	function init() {

		// Base URL for remote validation directive
		_this.config = {baseUrl : CONFIGURATION.root};	
	
		function handleSuccess(data, status) {
			_this.movie = data;
		}

		//Grab movieID off of the route        
		var movieId = ($routeParams.id) ? parseInt($routeParams.id) : 0;
		if (movieId > 0) {
			_this.movie = movieFactory.getMovie(movieId).success(handleSuccess);
		}
		
		_this.movie.data = new Date(_this.movie.data)
	}

}

// modifica
controllers.detailsMovieController = function ($routeParams, $fileUploader, $scope, movieFactory, CONFIGURATION) {

	var _this = this;

	_this.movie = {};

	init();

	function init() {

		// Form submit status
		_this.submitStatus = false;

		// Base URL for remote validation directive
		_this.config = {baseUrl : CONFIGURATION.root};

        // create a uploader with options
        var uploader = $scope.uploader = $fileUploader.create({
            scope: $scope,                          // to automatically update the html. Default: $rootScope
            url: '/ngMovieNet/ws/upload/locandina'
        });

        // REGISTER UPLOADER HANDLERS
        uploader.bind('afteraddingfile', function (event, item) {
            console.info('After adding a file', item);
        });

        uploader.bind('success', function (event, xhr, item, response) {
        	if (item.isUploaded)
        	{
        		_this.movie.locandina = response.filename; 
        		console.log(_this.movie.locandina);
        	}
        });   		

		_this.getRegisti = function(val) {
			return movieFactory.getRegisti(val);
		};
		
		_this.getGeneri = function(val) {
			return movieFactory.getGeneri(val);	
		};		
	
		function handleSuccess(data, status) {
			_this.movie = data;
		}

		//Grab movieID off of the route        
		var movieId = ($routeParams.id) ? parseInt($routeParams.id) : 0;
		if (movieId > 0) {
			_this.movie = movieFactory.getMovieToEdit(movieId).success(handleSuccess);
		}
		
		_this.movie.data = new Date(_this.movie.data)

		// Datepicker stuff

		// Disable weekend selection
		_this.disabled = function(date, mode) {
		  return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
		};
		
		_this.open = function($event) {
			$event.preventDefault();
			$event.stopPropagation();
			
			_this.opened = true;
		};
		
		_this.dateOptions = {
			'year-format': "'yy'",
			'starting-day': 1,
			'show-button-bar' : false,
		};		
	}

	_this.salva = function() {
		_this.movie.data = new Date(_this.movie.data).yyyymmdd();
		movieFactory.updateMovie(_this.movie).success(function(data) {
			if (data.status === 'ok')
			{
				_this.messaggio = 'Modifiche salvate.';
				_this.submitStatus = true;
			}
			else
			{
				_this.messaggio = 'Errore salvataggio.';
				_this.submitStatus = false;
			}
		});
	};
}

// inserimento
controllers.newMovieController = function (movieFactory, $fileUploader, $scope, CONFIGURATION) {

	var _this = this;

	init();
	
	function init()
	{ 

		// Form submit status
		_this.submitStatus = false;

		// Base URL for remote validation directive
		_this.config = {baseUrl : CONFIGURATION.root};

        // create a uploader with options
        var uploader = $scope.uploader = $fileUploader.create({
            scope: $scope,                          // to automatically update the html. Default: $rootScope
            url: '/ngMovieNet/ws/upload/locandina'
        });

        // REGISTER UPLOADER HANDLERS
        uploader.bind('afteraddingfile', function (event, item) {
            console.info('After adding a file', item);
        });

        uploader.bind('success', function (event, xhr, item, response) {
        	if (item.isUploaded)
        	{
        		_this.movie.locandina = response.filename;
        	}
        });        

		_this.getRegisti = function(val) {
			return movieFactory.getRegisti(val);
		};
		
		_this.getGeneri = function(val) {
			return movieFactory.getGeneri(val);	
		};

		// Datepicker stuff

		// Disable weekend selection
		_this.disabled = function(date, mode) {
		  return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
		};
		
		_this.open = function($event) {
			$event.preventDefault();
			$event.stopPropagation();
			
			_this.opened = true;
		};
		
		_this.dateOptions = {
			'year-format': "'yy'",
			'starting-day': 1,
			'show-button-bar' : false,
		};
	
	}
	_this.salva = function() {
		
		_this.movie.data = new Date(_this.movie.data).yyyymmdd();
		movieFactory
			.setMovie(_this.movie)
			.success(function(data) {
				if (data.status === 'ok')
				{
					_this.messaggio = 'Nuovo film salvato.';
					_this.movie = [];
					_this.submitStatus = true;
				}
				else 
				{
					_this.messaggio = 'Ah! Errore!';	
					_this.submitStatus = false;
				}
			});
	} 
}

controllers.directorsListCtrl = function(directorsFactory) {
	var _this = this;
	
	init();
	
	function init() {
		_this.directors = [];

		directorsFactory.getAll().success(handleSuccess);
	}

	function handleSuccess(data, status) {
		_this.directors = data;
	}
};

app.controller(controllers);