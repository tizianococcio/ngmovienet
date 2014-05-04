// Lista factory

app.factory('listaFactory', ['$http', 'CONFIGURATION', function($http, conf) {
	var factory = {};
	
	// Salva una lista
	factory.setLista = function(lista) {
		return $http.post(conf.root + 'ws/liste/crea', lista);
	};

	// Tutte le liste
	factory.getListe = function(lista) {
		return $http.get(conf.root + 'ws/liste/elenca');
	};

	// Dettagli di una lista
	factory.getLista = function(id) {
		return $http.get(conf.root + 'ws/liste/dettaglio/' + id);
	}

	// Associa un film a una lista
	factory.linkMovie = function(movieId, listId) {
		return $http.post(conf.root + 'ws/liste/link', { id_film: movieId, id_lista: listId});
	};

	// Deassocia un film da una lista
	factory.unlinkMovie = function(movieId, listId) {
		return $http.post(conf.root + 'ws/liste/unlink', { id_film: movieId, id_lista: listId});
	};

	// Restituisce tutti i film in una lista
	factory.getMoviesInList = function(id) {
		return $http.get(conf.root + 'ws/liste/' + id + '/movies');
	};
	
	return factory;
}]);