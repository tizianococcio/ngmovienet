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

// Home page controller
controllers.homeController = function (){
	

	var _this = this;

	_this.names = [];
	
	init();
	
	function init() {	
	}

};

controllers.movieController = function (movieFactory) {

	var _this = this;

	_this.movies = [];

	init();
	
	function handleSuccess(data, status) {
		_this.movies = data;
	}
	
	function init() {
		_this.movies = movieFactory.getMovies();
	}
	
	movieFactory.getMovies().success(handleSuccess);
	
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

app.controller(controllers);