<?php
include ('slim_boot.php');

include ('../db/Database.php');

define ('APP_ROOT', '/ngMovieNet/');

include ($_SERVER['DOCUMENT_ROOT'] . APP_ROOT . 'ws/Film.php');

$app->db = Database::Load();

$app->cFilm = new Film($app->db);

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
    echo 'OK';
});

// Dettaglio film
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
				"id" => 			$d->id,
				"posizione" => 		prepareForJSON($d->supporto),
				"tipo_supporto" => 	prepareForJSON($d->tipo_supporto),
				"titolo" => 		prepareForJSON($d->titolo),
				"sotto_titolo" => 	prepareForJSON($d->sottotitolo),
				"regista" =>		prepareForJSON($d->regista),
				"genere" =>			prepareForJSON($d->genere),
				"cast" =>			prepareForJSON($d->cast),
				"data_uscita" =>	prepareForJSON($d->data_uscita),
				"locandina" =>		$d->locandina? prepareForJSON($url_locandina . $d->locandina) : 0,
				"trama" =>			prepareForJSON($d->trama),
				"durata" =>			prepareForJSON($d->durata)
			);
		}
	}

	echo json_encode($film);

});

function prepareForJSON($s)
{
	return ($s? utf8_encode($s) : "");
}

// Lista Film
$app->get('/film', function() use ($app) {
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
					"titolo" => utf8_encode($d->titolo)
				  );
	}
	echo json_encode($film);
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
		$data[] = array('id' => $d->id, 'nome' => utf8_encode($d->name));
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
		$data[] = array('id' => $d->id, 'nome' => utf8_encode($d->name));
	}
	
	echo json_encode($data);
});

// inserisce un film
$app->post('/film', function() use ($app) {
	$tabella = "movie";
	$film = json_decode($app->request()->getBody(), true);
	$id_genere = $id_regista = 0;

	// generi
	if (isset($film['genere']) && !empty($film['genere'])) 
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
	if (isset($film['regista']) && !empty($film['regista']))
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
			(titolo, sottotitolo, titolo_originale, id_genere, id_regista, supporto, data_uscita, cast, trama, durata) 
		  VALUES 
		  	(:titolo, :sottotitolo, :titolo_originale, :genere, :regista, :supporto, :data_uscita, :cast, :trama, :durata)";
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
	$st->execute();

	$output = array();
	if ($st->rowCount() > 0)
	{
		$id = $app->db->lastInsertId();
		$output = array('status' => 'ok', 'id' => $id);
	} else {
		$output = array('status' => 'error', 'id' => -1);
	}

	echo json_encode($output);


});

// Aggiorna film
$app->put('/film/:id', function($id) use ($app) {

	$film = json_decode($app->request()->getBody(), true);

	// generi
	$id_genere = $app->cFilm->salvaGenere($film['genere']);
	//$id_genere = 0;


	// registi
	$id_regista = $app->cFilm->salvaRegista($film['regista']);
	//$id_regista = 0;

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
			`id_regista` = :id_regista
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
	//$st->bindValue(':locandina', $film['locandina'], PDO::PARAM_STR);
	$st->bindValue(':id_genere', $id_genere, PDO::PARAM_STR);
	$st->bindValue(':id_regista', $id_regista, PDO::PARAM_STR);
	$st->bindValue(':id', $film['id'], PDO::PARAM_STR);
	$st->execute();

	$output = array('status' => 'ok');

	echo json_encode($output);

});



$app->run();


?>