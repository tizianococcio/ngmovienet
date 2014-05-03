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
	
	return factory;
}]);