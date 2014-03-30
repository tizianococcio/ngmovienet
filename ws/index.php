<?php
include ('slim_boot.php');

include ('../db/Database.php');

$app->db = Database::Load();

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


$app->run();


?>