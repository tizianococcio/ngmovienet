<?php
include ('slim_boot.php');

include ('../db/Database.php');

define ('APP_ROOT', '/ngMovieNet/');

include ($_SERVER['DOCUMENT_ROOT'] . APP_ROOT . 'ws/Film.php');

$app->db = Database::Load();

$app->cFilm = new Film($app->db);

// Struttura film
$app->film = array(
	'titolo' => '',
	'sottotitolo' => '',
	'titolo_originale' => '',
	'posizione' => '',
	'data' => '',
	'cast' => '',
	'trama' => '',
	'durata' => '',
	'locandina' => '',
);

/**
 * Azione per pagine non trovate
 */
$app->notFound(function () use ($app) {
    $app->redirect($app->baseUrl);
});

/**
 * Invoked before each function
 */
$app->hook('slim.before', function () use ($app) {
	
});

// Test
$app->get('/', function() use ($app) {
    echo 'OK - API is well and running.';
});

$app->get('/util', function() use ($app) {
	/*
    echo 'OK';
	$q = "SELECT * FROM movie where id != 879";
	$st = $app->db->prepare($q);
	$st->execute();
	while ($d = $st->fetchObject())
	{
		$uq = "UPDATE movie SET trama = :t, cast = :c WHERE id = " . $d->id;
		$stu = $app->db->prepare($uq);
		$stu->bindValue(":t", utf8_decode($d->trama), PDO::PARAM_STR);
		$stu->bindValue(":c", utf8_decode($d->cast), PDO::PARAM_STR);
		$stu->execute();
		//echo utf8_decode($d->trama);
	}
	echo ' - Done.';
	 */
});

// Dettaglio film per mostrare
$app->get('/film/:id', function($id) use ($app) {
	$url_locandina = "http://tiziano.patriziatrevisiartgallery.it/archivio-film/data/immagini/locandine/";

	$film = array();

	$q = "SELECT f.*, r.name AS regista, g.name AS genere 
			FROM movie f
			LEFT JOIN Genre g ON f.id_genere = g.id
			LEFT JOIN Director r ON f.id_regista = r.id 
			WHERE f.id = :id";
	$st = $app->db->prepare($q);
	$st->bindValue(":id", $id, PDO::PARAM_INT);
	if ($st->execute())
	{
		if ($st->rowCount() > 0)
		{
			$d = $st->fetchObject();
			$film = array(
				"id" => 				$d->id,
				"posizione" => 			prepareForJSON($d->supporto),
				"tipo_supporto" => 		prepareForJSON($d->tipo_supporto),
				"titolo" => 			prepareForJSON($d->titolo),
				"titolo_originale" => 	prepareForJSON($d->titolo_originale),
				"sotto_titolo" => 		prepareForJSON($d->sottotitolo),
				"regista" =>			prepareForJSON($d->regista),
				"genere" =>				prepareForJSON($d->genere),
				"cast" =>				prepareForJSON($d->cast),
				"data_uscita" =>		prepareForJSON($d->data_uscita),
				"locandina" =>			$d->locandina? prepareForJSON($url_locandina . $d->locandina) : 0,
				"trama" =>				prepareForJSON($d->trama),
				"durata" =>				prepareForJSON($d->durata)
			);
		}
	}

	echo json_encode($film);
});

// Dettaglio film per edit
$app->get('/film-edit-data/:id', function($id) use ($app) {

	$film = array();

	$q = "SELECT f.*, r.name AS regista, g.name AS genere 
			FROM movie f
			LEFT JOIN Genre g ON f.id_genere = g.id
			LEFT JOIN Director r ON f.id_regista = r.id 
			WHERE f.id = :id";
	$st = $app->db->prepare($q);
	$st->bindValue(":id", $id, PDO::PARAM_INT);
	if ($st->execute())
	{
		if ($st->rowCount() > 0)
		{
			$d = $st->fetchObject();
			$film = array(
				"id" => 				$d->id,
				"posizione" => 			prepareForJSON($d->supporto),
				"tipo_supporto" => 		prepareForJSON($d->tipo_supporto),
				"titolo" => 			prepareForJSON($d->titolo),
				"titolo_originale" => 	prepareForJSON($d->titolo_originale),
				"sotto_titolo" => 		prepareForJSON($d->sottotitolo),
				"regista" =>			prepareForJSON($d->regista),
				"genere" =>				prepareForJSON($d->genere),
				"cast" =>				prepareForJSON($d->cast),
				"data_uscita" =>		prepareForJSON($d->data_uscita),
				"locandina" =>			$d->locandina? prepareForJSON($d->locandina) : 0,
				"trama" =>				prepareForJSON($d->trama),
				"durata" =>				prepareForJSON($d->durata)
			);
		}
	}

	echo json_encode($film);
});

function prepareForJSON($s)
{
	return $s;
	//return ($s? utf8_encode($s) : "");
}

// Lista Film
$app->group('/film', function() use ($app) {


	$app->get('/', function() use ($app) {
		$film = array();

		$q = "SELECT id, supporto, tipo_supporto, titolo FROM movie ORDER BY titolo";
		$st = $app->db->prepare($q);
		$st->execute();

		while ($d = $st->fetchObject())
		{
			$film[] = array(
						"id" => $d->id, 
						"posizione" => $d->supporto,
						"supporto" => $d->tipo_supporto,
						"titolo" => $d->titolo
					  );
		}
		echo json_encode($film);
	});


	// Film per regista
	$app->get('/byDirector/:id', function($id) use ($app) {
		$film = array();

		$q = "SELECT id, supporto, tipo_supporto, titolo FROM movie WHERE id_regista = :regista ORDER BY titolo";
		$st = $app->db->prepare($q);
		$st->bindValue(":regista", $id, PDO::PARAM_INT);
		$st->execute();

		while ($d = $st->fetchObject())
		{
			$film[] = array(
						"id" => $d->id, 
						"posizione" => $d->supporto,
						"supporto" => $d->tipo_supporto,
						"titolo" => $d->titolo
					  );
		}
		echo json_encode($film);
	});


});

// Generi
$app->get('/generi', function () use ($app) {
	$get = $app->request()->get('s');
	$st = $app->db->prepare("SELECT * FROM Genre WHERE name LIKE :name ORDER BY name LIMIT 8");
	$st->bindValue(":name", "%" . $get . "%", PDO::PARAM_STR);
	$st->execute();
	
	$data = array();
	while ($d = $st->fetchObject())
	{
		$data[] = array('id' => $d->id, 'nome' => $d->name);
	}
	
	echo json_encode($data);
});

// Registi
$app->get('/registi', function () use ($app) {
	$get = $app->request()->get('s');
	$st = $app->db->prepare("SELECT * FROM Director WHERE name LIKE :name ORDER BY name LIMIT 8");
	$st->bindValue(":name", "%" . $get . "%", PDO::PARAM_STR);
	$st->execute();
	
	$data = array();
	while ($d = $st->fetchObject())
	{
		$data[] = array('id' => $d->id, 'nome' => $d->name);
	}
	
	echo json_encode($data);
});

// Inserisce un film
$app->post('/film', function() use ($app) {
	$tabella = "movie";
	$film = json_decode($app->request()->getBody(), true);
	$film = array_merge($app->film, $film);

	//print_r($film);
	//die();

	$id_genere = $id_regista = 0;

	// generi
	if (!empty($film['genere'])) 
	{
		// Se $film['genere'] è un array si tratta di un genere già presente nel db
		if (is_array($film['genere']))
		{
			$id_genere = $film['genere']['id'];
		}
		else
		{
			$id_genere = $app->cFilm->salvaGenere($film['genere']);
		}
	}
	
	// registi
	if (!empty($film['regista']))
	{
		// Se $film['regista'] è un array si tratta di un regista già presente nel db
		if (is_array($film['regista']))
		{
			$id_regista = $film['regista']['id'];
		}
		else
		{
			$id_regista = $app->cFilm->salvaRegista($film['regista']);
		}
	}

	// Inserimento database
	$q = "INSERT INTO {$tabella} 
			(titolo, sottotitolo, titolo_originale, id_genere, id_regista, supporto, data_uscita, cast, trama, durata, locandina) 
		  VALUES 
		  	(:titolo, :sottotitolo, :titolo_originale, :genere, :regista, :supporto, :data_uscita, :cast, :trama, :durata, :locandina)";
	$st = $app->db->prepare($q);
	$st->bindValue(":titolo", $film['titolo'], PDO::PARAM_STR);
	$st->bindValue(":sottotitolo", $film['sottotitolo'], PDO::PARAM_STR);
	$st->bindValue(":titolo_originale", $film['titolo_originale'], PDO::PARAM_STR);
	$st->bindValue(":genere", $id_genere, PDO::PARAM_INT);
	$st->bindValue(":regista", $id_regista, PDO::PARAM_INT);
	$st->bindValue(":supporto", $film['posizione'], PDO::PARAM_STR);
	$st->bindValue(":data_uscita", $film['data'], PDO::PARAM_STR);
	$st->bindValue(":cast", $film['cast'], PDO::PARAM_STR);
	$st->bindValue(":trama", $film['trama'], PDO::PARAM_STR);
	$st->bindValue(":durata", $film['durata'], PDO::PARAM_STR);
	$st->bindValue(":locandina", $film['locandina'], PDO::PARAM_STR);

	$st->execute();
	
	$output = array();
	if ($st->rowCount() > 0)
	{
		$id = $app->db->lastInsertId();
		$output = array('status' => 'ok', 'id' => $id);
	} else {
		$info = $st->errorInfo();
		$output = array('status' => 'error', 'id' => -1, 'details' => $info[2]);
	}

	echo json_encode($output);
});

// Aggiorna film
$app->put('/film/:id', function($id) use ($app) {

	$film = json_decode($app->request()->getBody(), true);

	// generi
	if (!empty($film['genere']))
	{
		if (is_array($film['genere']))
		{
			$id_genere = $film['genere']['id'];
		}
		else
		{
			$id_genere = $app->cFilm->salvaGenere($film['genere']);
		}		
	}

	// registi
	if (!empty($film['regista']))
	{
		if (is_array($film['regista']))
		{
			$id_regista = $film['regista']['id'];
		}
		else
		{
			$id_regista = $app->cFilm->salvaRegista($film['regista']);
		}
	}

	$q = "UPDATE `movie`
			SET
			`titolo` = :titolo,
			`sottotitolo` = :sottotitolo,
			`supporto` = :supporto,
			`tipo_supporto` = :tipo_supporto,
			`data_uscita` = :data_uscita,
			`cast` = :cast,
			`trama` = :trama,
			`durata` = :durata,
			`id_genere` = :id_genere,
			`id_regista` = :id_regista,
			`locandina` = :locandina
			WHERE `id` = :id;
			";
	$st = $app->db->prepare($q);
	$st->bindValue(':titolo', $film['titolo'], PDO::PARAM_STR);
	$st->bindValue(':sottotitolo', $film['sotto_titolo'], PDO::PARAM_STR);
	$st->bindValue(':supporto', $film['posizione'], PDO::PARAM_STR);
	$st->bindValue(':tipo_supporto', $film['tipo_supporto'], PDO::PARAM_STR);
	$st->bindValue(':data_uscita', $film['data_uscita'], PDO::PARAM_STR);
	$st->bindValue(':cast', $film['cast'], PDO::PARAM_STR);
	$st->bindValue(':trama', $film['trama'], PDO::PARAM_STR);
	$st->bindValue(':durata', $film['durata'], PDO::PARAM_STR);
	$st->bindValue(':locandina', $film['locandina'], PDO::PARAM_STR);
	$st->bindValue(':id_genere', $id_genere, PDO::PARAM_STR);
	$st->bindValue(':id_regista', $id_regista, PDO::PARAM_STR);
	$st->bindValue(':id', $film['id'], PDO::PARAM_STR);
	$st->execute();

	$output = array('status' => 'ok');

	echo json_encode($output);

});

// Upload
$app->group('/upload', function() use ($app){

	// Locandina
	$app->post('/locandina', function() use ($app){

		if ( !empty( $_FILES ) ) {

		    $tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
		    $fileName = uniqid() . '_' . ((strlen($_FILES[ 'file' ][ 'name' ]) > 8) ? substr($_FILES[ 'file' ][ 'name' ], 0, 8) : $_FILES[ 'file' ][ 'name' ]);
		    $uploadPath = $app->uploadFolder . $fileName;

		    move_uploaded_file( $tempPath, $uploadPath );

		    $answer = array( 'answer' => 'File transfer completed', 'status' => 'ok', 'filename' => $fileName);
		    $json = json_encode( $answer );

		    echo $json;

		} else {

		    echo 'No files';

		}
	});
});

// Liste
$app->group('/liste', function() use ($app){

	// Crea lista
	$app->post('/crea', function() use ($app){
		$dati = json_decode($app->request()->getBody(), true);
		$nome = $dati['nome'];

		$q = "INSERT INTO liste (nome) VALUES (:nome)";
		$st = $app->db->prepare($q);
		$st->bindValue(":nome", $nome, PDO::PARAM_STR);
		if ($st->execute())
		{
			$output = array('status' => 'ok');
			echo json_encode($output);
		}
	});

	// Elenca liste
	$app->get('/elenca', function() use ($app){

		$data = array();

		$q = "SELECT * FROM liste ORDER BY last_used DESC, nome";
		$st = $app->db->prepare($q);
		if ($st->execute())
		{
			while ($d = $st->fetchObject())
			{
				$data[] = array('id' => $d->id, 'nome' => $d->nome);
			}
			echo json_encode($data);
		}
	});	

	// associa un film ad una lista
	$app->post('/associa', function() use ($app){
		$dati = json_decode($app->request()->getBody(), true);
		$id_film = $dati['id_film'];
		$id_lista = $dati['id_lista'];

		$q = "INSERT INTO liste_movie (id_film, id_lista) VALUES (:id_film, :id_lista)";
		$st = $app->db->prepare($q);
		$st->bindValue(":id_film", $id_film, PDO::PARAM_INT);
		$st->bindValue(":id_lista", $id_lista, PDO::PARAM_INT);
		if ($st->execute())
		{

			// aggiorno last used
			$qu = "UPDATE liste SET last_used = NOW() WHERE id = :id";
			$stu = $app->db->prepare($qu);
			$stu->bindValue(":id", $id_lista, PDO::PARAM_INT);
			$stu->execute();

			$output = array('status' => 'ok');
			echo json_encode($output);
		}
	});
});

// Check if movie exists
$app->post('/check-if-movie-exists', function() use ($app) {
	$dati = json_decode($app->request()->getBody(), true);

	$titolo = strtolower($dati['value']);

	$return = array('isValid' => true);

	if ($app->cFilm->checkExistance($titolo))
	{
		// movie exists, title is not valid
		$return['isValid'] = false;
	}

	echo json_encode($return);
});

// Directors
$app->group('/directors', function() use ($app){

	$app->get('/', function() use ($app){

		$st = $app->db->prepare("SELECT * FROM Director ORDER BY name");
		$st->execute();
		
		$data = array();
		while ($d = $st->fetchObject())
		{
			$data[] = array('id' => $d->id, 'nome' => $d->name);
		}
		
		echo json_encode($data);		
	});

	$app->get('/:id', function($id) use ($app){

		$st = $app->db->prepare("SELECT * FROM Director WHERE id = :id ORDER BY name");
		$st->bindValue(":id", $id, PDO::PARAM_INT);
		$st->execute();
		
		$data = array();
		while ($d = $st->fetchObject())
		{
			$data = array('id' => $d->id, 'nome' => $d->name);
		}
		
		echo json_encode($data);		
	});	

});


$app->run();


?>