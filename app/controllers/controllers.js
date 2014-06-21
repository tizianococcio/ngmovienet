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

// Lista liste
controllers.ListController = function (listaFactory){
	

	var _this = this;
	
	init();
	
	function init() {	
		_this.lists = [];
		listaFactory.getListe().success(handleSuccess);
	}

	function handleSuccess(data, status)
	{
		_this.lists = data;
	}
};

// dettaglio lista (mostra film)
controllers.detailsListController = function (listaFactory, $routeParams){
	
	var _this = this;
	
	init();
	
	function init() {	
		_this.movies = [];
		_this.lista = [];

		var id = $routeParams.id ? $routeParams.id : 0;

		if (id > 0)
		{
			listaFactory.getMoviesInList(id).success(handleSuccess);
			listaFactory.getLista(id).success(handleListDetailSuccess);
		}
	}

	function handleSuccess(data, status)
	{
		_this.movies = data;
	}

	function handleListDetailSuccess(data, status)
	{
		_this.lista = data;
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
controllers.movieController = function ($scope, movieFactory, listaFactory, directorsFactory, Pagination, $routeParams, $location) {

	var _this = this;

	_this.movies = [];

	_this.liste = [];

	_this.director = [];

	init();

	function updatePageURLParam(pag) {
		$location.search('page', pag);
	}
	
	function handleSuccess(data, status) {

		var locationSearch = $location.search();

		// if a page is set, starts pagination from there
		var page = locationSearch.page ? locationSearch.page : 1;

		_this.pagination = Pagination.getNew();

		_this.pagination.page = page - 1;
		_this.pagination.perPage = 15;
		_this.pagination.range = 2;
		_this.pagination.setCallback(updatePageURLParam);

		_this.movies = data;

		_this.pagination.numPages = Math.ceil(_this.movies.length/_this.pagination.perPage);
	}

	function handleListeSuccess(data, status) {
		_this.liste = data;
	}	

	function handleDirectorSuccess(data, status) {
		_this.director = data;
	}

	function handleLinkListSuccess(data, status) {
		console.log('Fatto!');
	}
	
	function init() {

		listaFactory.getListe().success(handleListeSuccess);

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

		// Links a movie to a list of favourites
		_this.linkMovieToList = function(movieId, listaId) {
			
			// get the numerical index of the object
			var listObjId = getListObjectId(listaId);

			// popover setup
			$('#popover-info-handle').popover({
				placement: 'bottom',
			});

			if (listObjId !== undefined)
			{
				// if already in the list removes it, otherwise adds it
				if ((foundAt = _this.liste[listObjId].movies.indexOf(movieId)) > -1)
				{
					// unlink the movie - remote call
					listaFactory.unlinkMovie(movieId, listaId).success(handleLinkListSuccess);

					// removes from client side model
					_this.liste[listObjId].movies.splice(foundAt, 1);

					// Showing popover
					$('#popover-info-handle').data('bs.popover').options.content = 'Film rimosso';
					$('#popover-info-handle').popover('show');
				}
				else
				{
					// link the movie - remote call
					listaFactory.linkMovie(movieId, listaId).success(handleLinkListSuccess);

					// add to client side model
					_this.liste[listObjId].movies.push(movieId.toString());

					// Showing popover
					$('#popover-info-handle').data('bs.popover').options.content = 'Film aggiunto';
					$('#popover-info-handle').popover('show');
				}

				// Hides the popover after 2 seconds
				window.setTimeout(function() {
					$('#popover-info-handle').popover('hide');
				}, 2000);
			}
		}
	}

	// gets the index of the object, in the array of objects
	function getListObjectId(listId)
	{
		var index = undefined;

		angular.forEach(_this.liste, function(value, key){
			if (parseInt(listId) == parseInt(value.id))
			{
				index = key;
			}
		});

		return index;
	}

	
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
	
	// init model
	_this.movie = {posizione: ""};

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
	};
	
	// get last numeric support
	_this.getLastNumericSupport = function() {
		var request = movieFactory.getLastNumericSupport();
		request.success(function(data, status, headers, config) {
			_this.movie.posizione = data.last_numeric_support;
		});
	};	
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