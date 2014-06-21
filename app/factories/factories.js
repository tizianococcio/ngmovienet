app.factory('simpleFactory', function() {
	var factory = {};
	var names = [
		{name: 'John Smith', city: 'New York'}, 
		{name: 'Hilary Small', city: 'New Jersey'}, 
		{name: 'Victor Mignatti', city: 'Michigan'}
	];
	factory.getCustomers = function() {
		return names;
	}
	return factory;
});

app.factory('movieFactory', ['$http', 'CONFIGURATION', function($http, conf) {

	var factory = {};

	// Get last numeric support id
	factory.getLastNumericSupport = function() {
		return $http.get(conf.root + 'ws/film/get-last-numeric-support');
	}
	
	// Tutti i film
	factory.getMovies = function() {
		return $http.get(conf.root + 'ws/film');
	};

	// Movies filtered by director
	factory.getMoviesByDirector = function(id) {
		return $http.get(conf.root + 'ws/film/byDirector/' + id);
	};

	
	// Mostra Un film
	factory.getMovie = function(id) {
		return $http.get(conf.root + 'ws/film/' + id);
	};
	
	// Dati di un film per edit
	factory.getMovieToEdit = function(id) {
		return $http.get(conf.root + 'ws/film-edit-data/' + id);
	};	
	
	// Salva un film
	factory.setMovie = function(movie) {
		return $http.post(conf.root + 'ws/film', movie);
	};

	// Aggiorna un film
	factory.updateMovie = function(movie) {
		return $http.put(conf.root + 'ws/film/' + movie.id, movie);
	};
	
	// Registi
	factory.getRegisti = function(val) 
	{
		return $http.get(conf.root + 'ws/registi', {
			// Passo come parametro get il valore della text input
			params: {
				s: val,
			}
			
		// Chiamata completata
		}).then(function(res){
			
			// Array vuoto per contenere la lista dei registi
			var registi = [];
			
			// Ciclo per ogni elemento dei registi
			angular.forEach(res.data, function(item){
				
				// Genero un oggetto e lo aggiungo all'array
				registi.push({id: item.id, nome: item.nome});
			});
			return registi;
		});
	};
	
	// Generi
	factory.getGeneri = function(val)
	{
		// Ritorno il valore restituito dalla funzione
		return $http.get(conf.root + 'ws/generi', {
			// Passo come parametro il valore della input text
			params: {
				s: val
			}
		})
		// Chiamata completata
		.then(function(res) {
			// Array per la lista dei generi
			var generi = [];
			
			// Ciclo nei dati provenienti dal server
			angular.forEach(res.data, function(item) {
				// Inserisco nell'array gli oggetti con i dati che mi servono
				generi.push({id: item.id, nome: item.nome});
			});
			return generi;
		});		
	}
		
	return factory;
}]);

app.factory('directorsFactory', ['$http', 'CONFIGURATION', function($http, conf) {
	var factory = {}
	
	// Get Directors
	factory.getAll = function() {
		return $http.get(conf.root + 'ws/directors');
	};

	// Get director data from ID
	factory.getDirector = function(id) {
		return $http.get(conf.root + 'ws/directors/' + id);
	};
	
	return factory;
}]);

// Modello di factory
app.factory('emptyFactory', function($http) {
	var factory = {}
	
	// Factory vuota
	
	return factory;
});

